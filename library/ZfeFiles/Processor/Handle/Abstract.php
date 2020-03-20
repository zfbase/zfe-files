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
    /**
     * Запланировать обработку файла.
     */
    abstract public static function addToPlan(ZfeFiles_FileInterface $file, array $options): ZfeFiles_Processor_Process_Interface;
    
    /**
     * Запланировать обработку файла.
     */
    abstract public function planFile(ZfeFiles_FileInterface $file): void;

    /**
     * Выполнить запланированные обработки.
     */
    abstract public static function progressPlan(): void;

    /**
     * Выполнить обработку.
     */
    abstract public function progress(): void;

    /**
     * Файл.
     *
     * @var ZfeFiles_FileInterface
     */
    protected $file;

    /**
     * Установить целевой файл.
     */
    public function setFile(ZfeFiles_FileInterface $file): ZfeFiles_Processor_Handle_Abstract
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
     * Параметры обработки.
     *
     * @var array
     */
    protected $options;

    /**
     * Установить параметры обработки.
     */
    public function setOptions(array $options = []): ZfeFiles_Processor_Handle_Abstract
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Установить параметр обработки.
     */
    public function setOption(string $name, $value): ZfeFiles_Processor_Handle_Abstract
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * Получить параметры обработки.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Получить название обработчика.
     */
    public function getName(): string
    {
        return static::class;
    }
}
