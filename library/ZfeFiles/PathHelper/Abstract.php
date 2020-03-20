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

    /**
     * Конструкторы.
     */
    public function __construct(ZfeFiles_FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Получить имя файла.
     */
    abstract public function getFileName(): string;

    /**
     * Получить директорию файла в хранилище.
     */
    abstract public function getDirectory($separator = DIRECTORY_SEPARATOR): string;

    /**
     * Получить адрес корня хранилища на диске.
     */
    abstract public function getRoot(): string;

    /**
     * получить адрес корня хранилища для веба.
     */
    abstract public function getWebRoot(): string;

    /**
     * Получить путь до файла в хранилище.
     */
    public function getPath($separator = DIRECTORY_SEPARATOR): string
    {
        return implode($separator, array_filter([
            $this->getDirectory($separator) ?: null,
            $this->getFileName(),
        ]));
    }

    /**
     * Получить полный до файла в хранилище.
     */
    public function getFullPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->getRoot(),
            $this->getPath(DIRECTORY_SEPARATOR),
        ]);
    }

    /**
     * Получить полный до файла в хранилище.
     */
    public function getFullWebPath(): string
    {
        return implode('/', [
            $this->getWebRoot(),
            $this->getPath('/'),
        ]);
    }
}
