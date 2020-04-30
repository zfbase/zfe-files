<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный контроллер для управления файлами.
 */
abstract class ZfeFiles_Controller_Default extends Controller_AbstractResource
{
    /**
     * Загрузить файл.
     */
    public function uploadAction(): void
    {
        try {
            $file = $this->getUploader()->upload($this->getAllParams());
            if ($file === null) {
                $this->_json(static::STATUS_SUCCESS);
            } else {
                $this->_json(
                    static::STATUS_SUCCESS,
                    ['file' => $file->getDataForUploader()],
                    "Файл {$file->title} успешно загружен."
                );
            }
        } catch (Exception $e) {
            $this->_json(static::STATUS_FAIL, [], 'Не удалось загрузить файл: ' . $e->getMessage());
        }
    }

    /**
     * Получить загрузчик файлов.
     */
    protected function getUploader(): ZfeFiles_Uploader_Interface
    {
        $config = Zend_Registry::get('config');
        $uploaderName = $config->files->uploader ?: ZfeFiles_Uploader_DefaultAjax::class;
        return new $uploaderName();
    }

    /**
     * Скачать файл.
     */
    public function downloadAction(): void
    {
        $file = $this->_loadItemOrFall();
        $this->_helper->download($file->path, '/download/' . $file->id, $file->title);
    }
}
