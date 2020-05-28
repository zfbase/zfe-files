<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный построитель пути для размещения файлов на диске.
 */
class ZfeFiles_PathHelper_Default
{
    /**
     * Файл.
     */
    protected ZfeFiles_File_Interface $file;

    /**
     * Разделитель директорий.
     */
    protected static string $separator = DIRECTORY_SEPARATOR;

    /**
     * Корневая директория файлов.
     */
    protected string $root;

    public function __construct(ZfeFiles_File_Interface $file, string $root)
    {
        $this->file = $file;
        $this->root = $root;
    }

    /**
     * Получить имя файла.
     *
     * @throws ZfeFiles_Exception
     */
    public function getFileName(): string
    {
        if (!$this->file->hash) {
            throw new ZfeFiles_Exception('Не возможно получить имя файла: не определена хеш-сумма');
        }

        return $this->file->hash . ($this->file->extension ? ".{$this->file->extension}" : '');
    }

    /**
     * Получить директорию файла в хранилище.
     *
     * @throws ZfeFiles_Exception
     */
    public function getDirectory(): ?string
    {
        if (!$this->file->id) {
            throw new ZfeFiles_Exception('Не возможно получить имя файла: не определен ID');
        }

        $subPathParts = str_split($this->file->id, ZfeFiles_Helpers::DIVIDE);
        return implode('/', $subPathParts);
    }

    /**
     * Получить адрес корня хранилища.
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Получить путь до файла в хранилище.
     *
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public function getPath(): string
    {
        return implode(static::$separator, array_filter([
            rtrim($this->getRoot(), static::$separator),
            $this->getDirectory() ?: null,
            $this->getFileName(),
        ]));
    }
}
