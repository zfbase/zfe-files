<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Простой стандартный контроллер файлов.
 */
class ZfeFiles_Controller_Default extends Controller_Abstract
{
    protected static $_modelName = Files::class;

    /**
     * Загрузить файлы.
     */
    public function uploadAction(): void
    {
        try {
            $uploader = new ZfeFiles_Uploader_DefaultAjax();
            $file = $uploader->upload();
            $this->_json(static::STATUS_SUCCESS, ['file' => $file->toAjaxData()], 'Файл загружен');
        } catch (Exception $e) {
            $this->_json(static::STATUS_FAIL, ['error' => $e->getMessage()], 'Не удалось загрузить файл');
        }
    }

    /**
     * Скачивание отдельных файлов.
     */
    public function downloadAction(): void
    {
        $file = $this->loadFile();
        $item = $file->getManageableItem();
        if (!$item) {
            $this->abort(404);
        }

        $fm = $item->getFileManager(true);
        $accessor = $fm->getAccessor();
        if (!$accessor->isAllowToDownload()) {
            $this->error('Скачивание файла запрещено');
        }

        $loader = $fm->getLoader()->setRecord($file);
        $this->_helper->download($loader->absFilePath(), '/download/' . $file->path, $file->title);
    }

    /**
     * Удалить файл.
     */
    public function deleteAction(): void
    {
        $file = $this->loadFile(false, true);
        if (!$file) {
            $this->error('Файл не найден.');
        }

        if ($file->isDeleted()) {
            $this->warning('Файл уже был удален ранее.');
        }

        $item = $file->getManageableItem();
        if ($item) {
            $fm = $item->getFileManager(true);
            $accessor = $fm->getAccessor();
            if (!$accessor->isAllowToDelete()) {
                $this->error('Удаление файла запрещено');
            }
        }

        try {
            $file->delete();
            $this->success('Файл `' . $file->title . '` успешно удален');
        } catch (Throwable $e) {
            $this->error('Не удалось удалить файл.');
        }
    }

    /**
     * Восстановить удаленный файл.
     */
    public function undeleteAction(): void
    {
    }

    /**
     * Показать список не привязанных файлов.
     */
    public function lostAction(): void
    {
    }

    /**
     * Запланировать обработку файла.
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Query_Exception
     * @throws Zend_Controller_Action_Exception
     */
    public function processAction(): void
    {
        $file = $this->loadFile();
        $item = $file->getManageableItem();
        if (!$item) {
            $this->abort(404);
        }

        $fm = $item->getFileManager(true);
        $accessor = $fm->getAccessor();
        if (!$accessor->isAllowToProcess()) {
            throw new Zend_Controller_Action_Exception('Доступ запрещен', 403);
        }

        $mapping = $file->getProcessings();  /** @var ZfeFiles_Processor_Mapping $mapping */
        foreach ($mapping as $modelName => $collection) {  /** @var Doctrine_Collection $collection */
            if ($collection->count() == 0) {
                $processing = new $modelName;  /** @var ZfeFiles_Model_Processing_Interface $processing */
                $processor = $processing->getProcessor();
                $processor->plan($file)->getProcessing()->save();

                $this->afterProccessingPlanned($item);

                ZFE_Notices::ok($processor->getDescription() . ' выполняется');
            }
        }

        /** @var Zend_Controller_Request_Http $req */
        $req = $this->getRequest();
        $this->redirect($req->getServer('HTTP_REFERER'));
    }

    /**
     * Загрузить файл.
     *
     * @param bool $itemOrFail  вернуть файл или упасть
     * @param bool $withDeleted искать в том числе среди удаленных
     *
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    protected function loadFile(bool $itemOrFail = true, bool $withDeleted = false): ?ZfeFiles_Model_File
    {
        $id = $this->getParam('id');
        if (!$id) {
            throw new BadMethodCallException('Не указан идентификатор файла.', 400);
        }

        $findMethod = $withDeleted ? 'hardFind' : 'find';
        $file = (static::$_modelName)::{$findMethod}($id);
        if (!$file && $itemOrFail) {
            throw new RuntimeException('Файл не найден.');
        }

        return $file;
    }

    /**
     * Хук.
     */
    protected function afterProccessingPlanned(ZfeFiles_Manageable $item): void
    {
    }
}
