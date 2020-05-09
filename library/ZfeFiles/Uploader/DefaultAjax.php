<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный загрузчик по средством Ajax.
 */
class ZfeFiles_Uploader_DefaultAjax implements ZfeFiles_Uploader_Interface
{
    /**
     * Конфигурация.
     */
    protected Zend_Config $config;

    /**
     * Название модели файла.
     */
    protected string $fileModelName;

    /**
     * Обработчик загрузки файла.
     */
    protected ZfeFiles_Uploader_Handler_Interface $uploadHandler;

    /**
     * Временная директория для загруженных файлов.
     */
    protected string $tempRoot;

    /**
     * Алгоритм расчета хеш-суммы от файла.
     */
    protected string $hashAlgo = 'md5';

    public function __construct(
        string $fileModelName = null,
        ZfeFiles_Uploader_Handler_Interface $uploadHandler = null,
        string $tempRoot = null
    )
    {
        try {
            $this->config = Zend_Registry::get('config');

            if (!$fileModelName) {
                $fileModelName = $this->config->files->modelName ?? null;
            }

            if (!$uploadHandler) {
                $uploadHandlerClass = $this->config->files->uploadHandler ?? null;
                if ($uploadHandlerClass) {
                    $uploadHandler = new $uploadHandlerClass;
                }
            }

            if (!$tempRoot) {
                $tempRoot = $this->config->files->tempPath ?? null;
            }

            $hashAlgo = $this->config->files->hashAlgo ?? null;
            if ($hashAlgo) {
                $this->hashAlgo = $hashAlgo;
            }
        } catch(Zend_Exception $e) {
        }

        $this->fileModelName = $fileModelName ?: Files::class;
        $this->uploadHandler = $uploadHandler ?: new ZfeFiles_Uploader_Handler_Default();
        $this->tempRoot = $tempRoot ?: sys_get_temp_dir();
    }

    /**
     * @inheritDoc
     *
     * @throws Zend_Session_Exception
     * @throws ZfeFiles_Uploader_Exception
     * @throws Exception
     */
    public function upload(array $params = []): ?ZfeFiles_FileInterface
    {
        $uploadResult = $this->uploadHandler->upload($params['field'] ?? 'file');

        $chunksCount = isset($params['chunksCount']) ? intval($params['chunksCount']) : null;
        $chunkNum = isset($params['chunkNum']) ? intval($params['chunkNum']) : null;
        $modelName = $params['modelName'] ?? null;
        $schemaCode = $params['schemaCode'] ?? null;
        $itemId = $params['itemId'] ?? null;
        $fileName = $uploadResult->getName();

        if ($chunksCount) {
            // Если грузим чанками, то через сессию контролируем загрузку.
            $session = new Zend_Session_Namespace('ChunksUploader-' . ($params['uid'] ?? null));
            if ($session->chunksCount === null) {
                $session->chunksCount = $chunksCount;
                $session->completeChunks = [];
                $session->chunkFilePrefix = uniqid();
            } elseif ($session->chunksCount !== $chunksCount) {
                throw new ZfeFiles_Uploader_Exception('Коллизия сессии загрузки файла чанками');
            }

            $chunkHash = $params['chunkHash'] ?? null;
            if ($chunkHash && $chunkHash != $uploadResult->getHash()) {
                throw new ZfeFiles_Uploader_Exception('Указанная хеш-сумма не совпадает с расчетной от загруженного чанка.');
            }

            $session->completeChunks[$chunkNum] = $uploadResult->getPath();

            if ($chunksCount === count($session->completeChunks)) {
                $fileSize = $params['fileSize'] ?? null;
                $fileName = $params['fileName'] ?? $uploadResult->getName();

                $tempPath = realpath($this->tempRoot) . DIRECTORY_SEPARATOR . time() . '-' . $fileName;
                $chunkPaths = $session->completeChunks;
                ksort($chunkPaths);  // выравнивание для многопоточной загрузки
                foreach ($chunkPaths as $chunkPath) {
                    $chunkSize = filesize($chunkPath);
                    $chunkFile = fopen($chunkPath, 'rb');
                    $chunkBlob = fread($chunkFile, $chunkSize);
                    fclose($chunkFile);

                    $tempFile = fopen($tempPath, 'ab');
                    fwrite($tempFile, $chunkBlob);
                    fclose($tempFile);

                    unlink($chunkPath);
                }
            } else {
                return null;
            }
        } else {
            $tempPath = $uploadResult->getPath();
            $fileName = $uploadResult->getName();
            $fileSize = $uploadResult->getSize();
        }

        $file = $this->createFile([
            'tempPath' => $tempPath,
            'fileName' => $fileName,
            'fileSize' => $fileSize,
            'modelName' => $modelName,
            'schemaCode' => $schemaCode,
            'itemId' => $itemId,
        ]);

        $this->processFile($file);

        return $file;
    }

    /**
     * Зарегистрировать файл.
     * 
     * @param array $data {
     *     @var string      $tempPath
     *     @var string      $fileName
     *     @var int         $fileSize
     *     @var string|null $modelName
     *     @var string|null $schemaCode
     *     @var int|null    $itemId
     * }
     *
     * @throws Exception
     * @throws ZfeFiles_Uploader_Exception
     */
    protected function createFile(array $data): ZfeFiles_FileInterface
    {
        /** @var ZfeFiles_FileInterface|Files $file */
        $file = new $this->fileModelName;
        $file->title = $data['fileName'];
        $file->size = $data['fileSize'];
        $file->model_name = $data['modelName'];
        $file->schema_code = $data['schemaCode'];
        $file->item_id = $data['itemId'];

        if ($file->contains('path')) {
            $file->path = $data['tempPath'];
        }

        if ($file->contains('extension')) {
            $file->extension = $this->getExtension($data['fileName']);
        }

        if ($file->contains('hash')) {
            if ($file instanceof ZfeFiles_FileHashableInterface) {
                $file->hash(false);
            } else {
                $file->hash = hash_file($this->hashAlgo, $data['tempPath']);
            }
        }

        $file->save();

        $newPath = $file->getRealPathHelper()->getPath(true);
        $this->checkDirectory($newPath);
        if (rename($data['tempPath'], $newPath)) {
            if ($file->contains('path')) {
                $file->path = $newPath;
                $file->save();
            }
        } else {
            throw new ZfeFiles_Uploader_Exception('Не удалось переложить загруженный файл из временной директории');
        }

        return $file;
    }

    /**
     * Выполнить все необходимые обработки файла.
     */
    protected function processFile(ZfeFiles_FileInterface $file): void
    {
        $schema = ZfeFiles_Dispatcher::getSchemaForFile($file);
        if ($schema) {
            $processor = $schema->getProcessor();
            if ($processor) {
                $processor->process($file);
            }
        }
    }

    /**
     * Получить расширение файла по имени файла.
     */
    protected function getExtension(string $fileName): ?string
    {
        $parts = explode('.', $fileName);
        $lastPart = end($parts);
        return mb_strtolower($lastPart);
    }

    /**
     * Проверить директорию для перемещения в нее файла.
     *
     * @throws ZfeFiles_Uploader_Exception
     */
    protected function checkDirectory(string $path): void
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!is_dir($dir)) {
            throw new ZfeFiles_Uploader_Exception('Не возможно переместить файл – конфликт имен.');
        }
    }
}
