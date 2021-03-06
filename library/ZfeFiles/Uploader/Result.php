<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Результат загрузки файла.
 * Возвращается обработчиком загрузки в загрузчик.
 */
class ZfeFiles_Uploader_Result
{
    /**
     * Оригинальное имя файла.
     */
    protected ?string $name = null;

    /**
     * Путь по которому сохранен файл.
     */
    protected ?string $path = null;

    /**
     * Размер файла в байтах.
     */
    protected ?int $size = null;

    /**
     * Хеш-сумма от файла.
     */
    protected ?string $hash = null;

    /**
     * Установить оригинальное имя файла.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получить оригинальное имя файла.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установить путь, по которому сохранен файл.
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Получить путь, по которому сохранен файл.
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Установить размер файла в байтах.
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Получить размер файла в байтах.
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Установить хеш-сумму от файла.
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Получить хеш-сумму от файла.
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
}
