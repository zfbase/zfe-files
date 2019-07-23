<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Базовый Ajax-загрузчик файлов.
 */
class ZfeFiles_Uploader_DefaultAjax extends ZfeFiles_Uploader_Abstract
{
    /**
     * Класс файлов.
     *
     * @var string
     */
    protected $fileModelName;

    /**
     * Помощник загрузки.
     *
     * @var ZfeFiles_Uploader_HandlerHelper_Interface
     */
    protected $handlerHelper;

    public function __construct(string $fileModelName = null, ZfeFiles_Uploader_HandlerHelper_Interface $handlerHelper = null)
    {
        $config = Zend_Registry::get('config');
        $this->fileModelName = $fileModelName ?: $config->files->modelName;
        $this->handlerHelper = $handlerHelper ?: $config->files->uploadHandler;

        if (!$this->fileModelName) {
            throw new ZfeFiles_Uploader_Exception('Не указана модель файлов.');
        }

        if (!$this->handlerHelper) {
            throw new ZfeFiles_Uploader_Exception('Не указан помощник загрузки.');
        }
    }

    /**
     * Загрузить файл.
     */
    public function upload(): ZfeFiles_Model_File
    {
        $result = $this->handlerHelper->handleUpload();

        if ($result->isSuccess()) {
            /** @var ZfeFiles_Model_File $file */
            $file = new $this->fileModelName;
            $file->title = $result->getName();
            $file->path = $result->getPath();
            $file->size = $result->getSize();
            $file->save();
            return $file;
        }

        throw new ZfeFiles_Uploader_Exception($result->getErrorMessage(), $result->getErrorCode());
    }
}
