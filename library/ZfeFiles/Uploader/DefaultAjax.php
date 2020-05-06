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

    public function __construct(
        string $fileModelName = null,
        ZfeFiles_Uploader_Handler_Interface $uploadHandler = null,
        string $tempRoot = null
    )
    {
        $this->fileModelName = $fileModelName;
        $this->tempRoot = $tempRoot;

        try {
            $this->config = Zend_Registry::get('config');

            if (!$this->fileModelName) {
                $this->fileModelName = $this->config->files->modelName;
            }

            if (!$uploadHandler) {
                $uploadHandlerClass = $this->config->files->uploadHandler;
            }

            if (!$this->tempRoot) {
                $this->tempRoot = $this->config->files->tempPath;
            }
        } catch(Zend_Exception $e) {
            $uploadHandlerClass = null;
        }

        if (!$this->fileModelName) {
            $this->fileModelName = 'Files';
        }

        if ($uploadHandler) {
            $this->uploadHandler = $uploadHandler;
        } else {
            if (!$uploadHandlerClass) {
                $uploadHandlerClass = ZfeFiles_Uploader_Handler_Default::class;
            }

            $this->uploadHandler = new $uploadHandlerClass;
        }

        if (!$this->tempRoot) {
            $this->tempRoot = sys_get_temp_dir();
        }
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
            'fileExt' => $this->getExtension($fileName),
            'modelName' => $modelName,
            'schemaCode' => $schemaCode,
            'itemId' => $itemId,
        ]);

        // Перекладываем по пути постоянного хранения.
        // До сохранения файла у нас нет его ID при этом важно всегда держать в $file->path актуальное расположения.
        $this->moveFile($file);

        $this->processFile($file);

        return $file;
    }

    /**
     * Зарегистрировать файл.
     *
     * @throws Exception
     */
    protected function createFile(array $data): ZfeFiles_FileInterface
    {
        /** @var ZfeFiles_FileInterface|Files $file */
        $file = new $this->fileModelName;
        $file->title = $data['fileName'];
        $file->path = $data['tempPath'];
        $file->size = $data['fileSize'];
        $file->ext = $data['fileExt'];
        $file->model_name = $data['modelName'];
        $file->schema_code = $data['schemaCode'];
        $file->item_id = $data['itemId'];
        $file->save();

        return $file;
    }

    /**
     * Переместить файл на постоянное хранение.
     *
     * @throws ZfeFiles_Uploader_Exception
     */
    protected function moveFile(ZfeFiles_FileInterface $file): void
    {
        $newPath = $file->getPathHelper()->getPath();
        if (rename($file->path, $newPath)) {
            $file->path = $newPath;
            $file->save();
        } else {
            throw new ZfeFiles_Uploader_Exception('Не удалось переложить загруженный файл из временной директории');
        }
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
}
