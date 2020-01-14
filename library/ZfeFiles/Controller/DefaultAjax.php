<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный контроллер для управления файлами посредством AJAX.
 * Единственный не Ajax-метод – lost.
 */
class ZfeFiles_Controller_DefaultAjax extends Controller_Abstract
{
    protected static $_modelName;

    /**
     * Загрузить файл.
     */
    public function uploadAction(): void
    {
        try {
            $file = $this->getUploader()->upload($this->getAllParams());
            $this->_json(
                static::STATUS_SUCCESS,
                ['file' => $file->getDataForUploader()],
                "Файл {$file->title} успешно загружен."
            );
        } catch (Exception $e) {
            $this->_json(static::STATUS_FAIL, [], 'Не удалось загрузить файл: ' . $e->getMessage());
        }
    }

    /**
     * Скачать файлы.
     */
    public function downloadAction(): void
    {
        $file = $this->loadFile();
        $this->_helper->download($file->path, '/download/' . $file->id, $file->title);
    }

    /**
     * Удалить файл.
     */
    public function deletedAction(): void
    {
    }

    /**
     * Восстановить удаленные файлы.
     */
    public function undeletedAction(): void
    {
    }

    /**
     * Показать непривязанные ни к кому файлы.
     */
    public function lostAction(): void
    {
    }

    /**
     * Получить загрузчик файлов.
     */
    protected function getUploader(): ZfeFiles_Uploader_Interface
    {
        $config = Zend_Registry::get('config');
        $uploaderName = $config->files->uploader;
        return new $uploaderName();
    }

    protected function loadFile(bool $withDeleted = false): ZfeFiles_FileInterface
    {
        $id = $this->getParam('id');
        if (!$id) {
            throw new BadMethodCallException('Не указан идентификатор файла.', 400);
        }

        $findMethod = $withDeleted ? 'hardFind' : 'find';
        $file = (static::$_modelName)::{$findMethod}($id);
        if (!$file) {
            throw new RuntimeException('Файл не найден.');
        }

        return $file;
    }
}
