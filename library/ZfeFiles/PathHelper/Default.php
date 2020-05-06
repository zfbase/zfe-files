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
    protected ZfeFiles_FileInterface $file;

    /**
     * Корневая директория файлов на диске.
     */
    protected string $root;

    /**
     * Конструкторы.
     */
    public function __construct(ZfeFiles_FileInterface $file)
    {
        $this->file = $file;
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

        return $this->file->hash;
    }

    /**
     * Получить директорию файла в хранилище.
     *
     * @throws ZfeFiles_Exception
     */
    public function getDirectory(string $separator = DIRECTORY_SEPARATOR): string
    {
        if (!$this->file->id) {
            throw new ZfeFiles_Exception('Не возможно получить имя файла: не определен ID');
        }

        return $this->file->id;
    }

    /**
     * Получить адрес корня хранилища.
     *
     * @throws Zend_Exception
     */
    public function getRoot(): string
    {
        if ($this->root === null) {
            $this->root = rtrim(Zend_Registry::get('config')->files->root, DIRECTORY_SEPARATOR);
        }

        return $this->root;
    }

    /**
     * Получить путь до файла в хранилище.
     *
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public function getPath(bool $full = false, string $separator = DIRECTORY_SEPARATOR): string
    {
        return implode($separator, array_filter([
            $full ? $this->getRoot() : null,
            $this->getDirectory($separator) ?: null,
            $this->getFileName(),
        ]));
    }
}
