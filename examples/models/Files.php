<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Пример модели, хранящей информацию о файлах.
 */
class Files extends BaseFiles implements ZfeFiles_FileInterface
{
    public static $nameSingular = 'Файл';
    public static $namePlural = 'Файлы';

    /**
     * @inheritDoc
     */
    public function getDataForUploader(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public static function getUploadUrl(): string
    {
        return '/' . static::getControllerName() . '/upload';
    }

    /**
     * @inheritDoc
     */
    public function getRealPathHelper(): ZfeFiles_PathHelper_Default
    {
        return new ZfeFiles_PathHelper_Default($this);
    }

    /**
     * @inheritDoc
     */
    public function getWebPathHelper(): ZfeFiles_PathHelper_DefaultWeb
    {
        return new ZfeFiles_PathHelper_DefaultWeb($this);
    }

    /**
     * @inheritDoc
     */
    public function getExportFileName(): string
    {
        return $this->title;
    }
}
