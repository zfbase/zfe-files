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
     * Название модели файла.
     *
     * @var string
     */
    protected $fileModelName;

    /**
     * Обработчик загрузки файла.
     *
     * @var ZfeFiles_Uploader_Handler_Interface
     */
    protected $uploadHandler;

    /**
     * Временная директория для загруженных файлов.
     *
     * @var string
     */
    protected $tempRoot;

    public function __construct(string $fileModelName = null, ZfeFiles_Uploader_Handler_Interface $uploadHandler = null)
    {
        $config = Zend_Registry::get('config');

        $this->fileModelName = $fileModelName ?: $config->files->modelName ?: 'Files';

        $uploadHandlerClass = $config->files->uploadHandler ?: ZfeFiles_Uploader_Handler_Default::class;
        $this->uploadHandler = $uploadHandler ?: new $uploadHandlerClass();

        $this->tempRoot = $config->files->tempPath ?? sys_get_temp_dir();
    }

    /**
     * Переопределить временную директорию для загруженных файлов.
     */
    public function setTempRoot(string $dir): ZfeFiles_Uploader_Interface
    {
        $this->tempRoot = $dir;
        return $this;
    }

    /**
     * {@inheritdoc}
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
        $fileExt = $uploadResult->getExtension();

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

        /** @var ZfeFiles_FileInterface $file */
        $file = new $this->fileModelName;
        $file->title = $fileName;
        $file->path = $tempPath;
        $file->size = $fileSize;
        $file->ext = $fileExt;
        $file->model_name = $modelName;
        $file->schema_code = $schemaCode;
        $file->item_id = $itemId;
        $file->save();

        // Перекладываем по пути постоянного хранения.
        // До сохранения файла у нас нет его ID при этом важно всегда держать в $file->path актуальное расположения.
        $newPath = $file->getPathHelper()->getPath();
        if (rename($tempPath, $newPath)) {
            $file->path = $newPath;
            $file->save();
        } else {
            trigger_error('Не удалось переложить загруженный файл из временной директории', E_USER_ERROR);
        }

        $schema = ZfeFiles_Dispatcher::getSchemaForFile($file);
        if ($schema) {
            $schema->getProcessor()->handleFile($file);
        }

        return $file;
    }
}
