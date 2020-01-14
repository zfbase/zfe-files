<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный построитель пути для размещения файлов.
 */
class ZfeFiles_PathHelper_Default extends ZfeFiles_PathHelper_Abstract
{
    /**
     * Корневая директория файлов.
     *
     * @var string
     */
    protected $root;

    /**
     * {@inheritdoc}
     */
    public function __construct(ZfeFiles_FileInterface $file)
    {
        parent::__construct($file);

        $config = Zend_Registry::get('config');
        $this->root = $config->files->root;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        if (!$this->file->exists()) {
            throw new ZfeFiles_Exception('Для получения пути на диске необходимо сохранить запись файла.');
        }

        return $this->root . DIRECTORY_SEPARATOR . $this->file->id;
    }
}
