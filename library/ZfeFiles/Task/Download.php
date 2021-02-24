<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Задача по URL.
 */
class ZfeFiles_Task_Download extends ZFE_Tasks_Performer
{
    /**
     * Модель файлов.
     */
    protected string $fileModel = 'Files';

    /**
     * Поле для указания URL источника.
     */
    protected string $urlField = 'source_url';

    /**
     * Модель файла.
     *
     * @var AbstractRecord|ZfeFiles_File_Interface|null
     */
    protected $file;

    /**
     * {@inheritdoc}
     */
    public static function getCode(): string
    {
        return 'FilesDownload';
    }

    protected function __construct()
    {
        $config = Zend_Registry::get('config')->get('files');

        if ($config && $config->fileModelName) {
            $this->fileModel = $config->fileModelName;
        }

        if ($config && $config->download && $config->downloader->urlField) {
            $this->urlField = $config->downloader->urlField;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function checkRelated(AbstractRecord $item): bool
    {
        return is_a($item, (new static)->fileModel);
    }

    /**
     * {@inheritdoc}
     */
    public function perform(int $relatedId, ?Zend_Log $logger = null): int
    {
        $this->file = ($this->fileModel)::find($relatedId);
        if (!$this->file) {
            throw new ZFE_Tasks_Performer_Exception('Файл не найден');
        }

        $url = $this->file->{$this->urlField};
        $tempPath = $this->download($url);

        $urlFragments = explode('/', $url);
        $fileName = end($urlFragments);

        /** @var ZfeFiles_Manager_Multiple */
        $manager = FilesImage::getManager();

        $this->file->hash = $manager->hash($tempPath);
        $this->file->save();

        $agent = $manager->factory([
            'tempPath' => $tempPath,
            'fileName' => $fileName,
            'fileExt' => $this->ext($tempPath),
            'hash' => $this->file->hash,
        ], true);
        $agent->process();

        return static::RETURN_CODE_SUCCESS;
    }

    /**
     * Скачать файл.
     *
     * @return string временный путь до скаченного файла
     */
    protected function download(string $url): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'ExFile_');
        $tempFile = fopen($tempPath, 'wb');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_FILE, $tempFile);
        curl_exec($curl);
        curl_close($curl);
        fclose($tempFile);

        return $tempPath;
    }

    /**
     * Определить расширение для файла.
     */
    protected function ext(string $path): ?string
    {
        return (new \Mimey\MimeTypes)->getExtension(mime_content_type($path));
    }
}
