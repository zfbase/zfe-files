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

    public function __construct(string $fileModelName = null, ZfeFiles_Uploader_Handler_Interface $uploadHandler = null)
    {
        $config = Zend_Registry::get('config');

        $this->fileModelName = $fileModelName ?: $config->files->modelName;
        $this->uploadHandler = $uploadHandler ?: new $config->files->uploadHandler;

        if (!$this->fileModelName) {
            throw new ZfeFiles_Exception('Не указано название модели файлов.');
        }

        if (!$this->uploadHandler) {
            throw new ZfeFiles_Exception('Не указан обработчик загрузки файлов.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function upload(array $params = []): ?ZfeFiles_FileInterface
    {
        $uploadResult = $this->uploadHandler->upload();

        /** @var ZfeFiles_FileInterface $file */
        $file = new $this->fileModelName;
        $file->title = $uploadResult->getName();
        $file->path = $uploadResult->getPath();
        $file->size = $uploadResult->getSize();
        $file->ext = $uploadResult->getExtension();

        $file->model_name = $params['model_name'] ?? null;
        $file->schema_code = $params['schema_code'] ?? null;
        $file->item_id = $params['item_id'] ?? null;

        $file->save();

        // Перекладываем по пути постоянного хранения.
        // До сохранения файла у нас нет его ID при этом важно всегда держать в $file->path актуальное расположения.
        $newPath = $file->getPathHelper()->getPath();
        move_uploaded_file($file->path, $newPath);
        $file->path = $newPath;
        $file->save();

        $schema = ZfeFiles_Dispatcher::getSchemaForFile($file);
        if ($schema) {
            $schema->getProcessor()->handleFile($file);
        }

        return $file;
    }
}
