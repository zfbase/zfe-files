<?php

class ZfeFiles_Uploader_HandlerHelper_Result
{
    /**
     * Оригинальное имя файла.
     *
     * @var string
     */
    protected $name;

    /**
     * Путь по которому сохранен файл.
     *
     * @var string
     */
    protected $path;

    /**
     * Размер файла в байтах.
     *
     * @var integer
     */
    protected $size;

    /**
     * Код ошибки.
     *
     * @var int
     */
    protected $errorCode;

    /**
     * Текст ошибки.
     *
     * @var string
     */
    protected $errorMessage;
    
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
     * Установить ошибку.
     */
    public function setError(string $message, int $code = null): self
    {
        $this->errorMessage = $message;
        $this->errorCode = $code;
        return $this;
    }

    /**
     * Получить код ошибки.
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Получить текст сообщения ошибки.
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Файл загружен успешно?
     */
    public function isSuccess(): bool
    {
        return !!$this->path && !$this->errorMessage && !$this->errorCode;
    }
}
