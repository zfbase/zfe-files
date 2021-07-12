<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный загрузчик файлов по URL.
 */
class ZfeFiles_Downloader_Default implements ZfeFiles_Downloader_Interface
{
    /**
     * Модель файлов.
     */
    protected string $fileModel;

    /**
     * Код задачи по скачиванию.
     */
    protected string $taskCode;

    /**
     * Поле для указания URL источника.
     */
    protected string $urlField;

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $fileModel = null, ?string $taskCode = null, ?string $urlField = null)
    {
        $this->fileModel = $fileModel ?: config('files.fileModelName', 'Files');
        $this->taskCode = $taskCode ?: config('files.downloader.taskCode', 'FilesDownload');
        $this->urlField = $urlField ?: config('files.downloader.urlField', 'source_url');

        if (!is_a($this->fileModel, ZfeFiles_File_Interface::class, true)) {
            throw new ZfeFiles_Exception(
                "Невозможно скачать файл – модель `{$this->fileModel}` не реализует интерфейс `ZfeFiles_File_Interface`"
            );
        }

        if (!Doctrine_Core::getTable($this->fileModel)->hasField($this->urlField)) {
            throw new ZfeFiles_Exception(
                "Невозможно скачать файл – в модели `{$this->fileModel}` отсутствует поле `{$this->urlField}`"
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function order(array $params): int
    {
        if (empty($params['url'])) {
            throw new ZfeFiles_Exception('Не указан адрес для загрузки');
        }

        $file = $this->createFile($params['url']);
        $this->planTask($file);
        return $file->id;
    }

    /**
     * Зарегистрировать файл.
     *
     * @return AbstractRecord|ZfeFiles_File_Interface
     */
    protected function createFile(string $url)
    {
        $file = new $this->fileModel;
        $file->source = $url;
        $file->save();
        return $file;
    }

    /**
     * Запланировать отложенную задачу по скачиванию.
     */
    protected function planTask(AbstractRecord $file): Tasks
    {
        $taskManager = ZFE_Tasks_Manager::getInstance();
        return $taskManager->plan($this->taskCode, $file);
    }
}
