<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Абстрактный обработчик файлов.
 * Обработчиками управляет процессор, реализующий ZfeFiles_Processor_Interface.
 */
abstract class ZfeFiles_Processor_Handle_Abstract
{
    const STATUS_WAIT = 0;  // Обработка еще не запущена.
    const STATUS_WORKING = 1;  // Обработка выполняется.
    const STATUS_COMPLETE = 2;  // Обработка успешно выполнена.
    const STATUS_ERROR = 3;  // Обработка завершилась с ошибкой.

    /**
     * Файл.
     *
     * @var ZfeFiles_FileInterface
     */
    protected $file;

    /**
     * Установить целевой файл.
     */
    public function setFile(ZfeFiles_FileInterface $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Получить целевой файл.
     */
    public function getFile(): ?ZfeFiles_FileInterface
    {
        return $this->file;
    }

    /**
     * Получить название обработчика.
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * Получить все процессы обработки.
     */
    abstract public function getAllProcesses(): array;

    /**
     * Получить процесс самой последней обработки.
     */
    abstract public function getLastProcess(): ?ZfeFiles_Processor_Process_Interface;

    /**
     * Получить статус обработки.
     */
    public function getStatus(): int
    {
        $lastProcess = $this->getLastProcess();
        if (!$lastProcess) {
            return static::STATUS_WAIT;
        }

        if ($lastProcess->isReady()) {
            return static::STATUS_COMPLETE;
        }

        if ($lastProcess->hasError()) {
            return static::STATUS_ERROR;
        }

        return static::STATUS_WORKING;
    }
}
