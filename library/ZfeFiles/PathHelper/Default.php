<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный построитель пути для размещения файлов.
 */
class ZfeFiles_PathHelper_Default extends ZfeFiles_PathHelper_Abstract
{
    /** {@inheritdoc} */
    public function getFileName(): string
    {
        if (!$this->id) {
            throw new ZfeFiles_Exception('Не возможно получить имя файла: не определен ID');
        }

        return $this->id;
    }

    /** {@inheritdoc} */
    public function getDirectory($separator = DIRECTORY_SEPARATOR): string
    {
        return null;
    }

    /**
     * Корневая директория файлов на диске.
     *
     * @var string
     */
    protected $root;

    /** {@inheritdoc} */
    public function getRoot(): string
    {
        if ($this->root === null) {
            $this->root = rtrim(Zend_Registry::get('config')->files->root, DIRECTORY_SEPARATOR);
        }

        return $this->root;
    }

    /**
     * Корневая директория файлов для веба.
     *
     * @var string
     */
    protected $webRoot;

    /** {@inheritdoc} */
    public function getWebRoot(): string
    {
        if ($this->webRoot === null) {
            $this->webRoot = rtrim(Zend_Registry::get('config')->files->webRoot, '/');
        }

        return $this->webRoot;
    }
}
