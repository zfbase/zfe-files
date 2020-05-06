<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный построитель пути для доступа к файлам через веб.
 */
class ZfeFiles_PathHelper_DefaultWeb extends ZfeFiles_PathHelper_Default
{
    /**
     * @inheritDoc
     */
    public function getFileName(): string
    {
        if (!$this->file->id) {
            throw new ZfeFiles_Exception('Не возможно получить имя файла: не определен ID');
        }

        return $this->file->id;
    }

    /**
     * @inheritDoc
     */
    public function getDirectory(string $separator = DIRECTORY_SEPARATOR): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRoot(): string
    {
        if ($this->root === null) {
            $this->root = '/' . ($this->file)::getControllerName();
        }

        return $this->root;
    }

    /**
     * Получить виртуальный адрес файла для скачивания через nginx.
     */
    public function getVirtualPath(): string
    {
        return '/download/' . $this->file->id;
    }
}