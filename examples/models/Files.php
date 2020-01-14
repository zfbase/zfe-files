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
     * {@inheritdoc}
     */
    public function getPathHelper(): ZfeFiles_PathHelper_Abstract
    {
        return new ZfeFiles_PathHelper_Default($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForUploader(): array
    {
        return $this->toArray();
    }
}
