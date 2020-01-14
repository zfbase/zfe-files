<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Построитель пути для размещения файлов.
 */
abstract class ZfeFiles_PathHelper_Abstract
{
    /**
     * Файл.
     *
     * @var ZfeFiles_FileInterface
     */
    protected $file;

    public function __construct(ZfeFiles_FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Получить путь до файла на диске.
     */
    abstract public function getPath(): string;
}
