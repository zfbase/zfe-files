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
     * @inheritDoc
     */
    protected static $_canCreate = false;

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
        try {
            $uploaderName = Zend_Registry::get('config')->files->uploader ?? null;
        } catch(Zend_Exception $ex) {
            $uploaderName = null;
        }

        if (empty($uploaderName)) {
            $uploaderName = ZfeFiles_Uploader_DefaultAjax::class;
        }

        return new $uploaderName();
    }

    /**
     * Скачать файл.
     *
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public function downloadAction(): void
    {
        /** @var ZfeFiles_FileInterface $file */
        $file = $this->_loadItemOrFall();
        $this->_helper->download(
            $file->getRealPathHelper()->getPath(true),
            $file->getWebPathHelper()->getVirtualPath(),
            $file->getExportFileName()
        );
    }
}
