<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Пример модели для файлов.
 */
class Files extends BaseFiles implements ZfeFiles_FileInterface
{
    public static $nameSingular = 'Файл';
    public static $namePlural = 'Файлы';

    /**
     * {@inheritdoc}
     */
    public function getRealPathHelper(): ZfeFiles_PathHelper_Default
    {
        $root = Zend_Registry::get('config')->files->root;
        return new ZfeFiles_PathHelper_Default($this, $root);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebPathHelper(): ZfeFiles_PathHelper_DefaultWeb
    {
        $root = '/' . self::getControllerName() . '/download/id/';
        return new ZfeFiles_PathHelper_DefaultWeb($this, $root);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        if ($this->exists()) {
            return empty($this->title)
                ? 'Без названия'
                : $this->title . ($this->extension ? ".{$this->extension}" : '');
        }

        return static::getNewTitle();
    }
}
