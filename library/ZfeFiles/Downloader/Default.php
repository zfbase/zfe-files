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
     * @inheritDoc
     */
    public function __construct(?string $fileModel = null, ?string $taskCode = null, ?string $urlField = null)
    {
        $config = $this->fromConfig();

        $this->fileModel = $fileModel ?: $config->fileModelName ?: 'Files';
        $this->taskCode = $taskCode ?: $config->taskCode ?: 'FilesDownload';
        $this->urlField = $urlField ?: $config->urlField ?: 'source_url';

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
     * @inheritDoc
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

    /**
     * Получить настройки из конфигурации.
     */
    protected function fromConfig(): stdClass
    {
        $config = Zend_Registry::get('config')->get('files');
        return (object) [
            'fileModel' => $config ? ($config->fileModelName ?? null) : null,
            'taskCode' => $config && isset($config->download) ? ($config->downloader->taskCode ?? null) : null,
            'urlField' => $config && isset($config->download) ? ($config->downloader->urlField ?? null) : null,
        ];
    }
}
