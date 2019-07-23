<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Абстрактный класс, скрывающий реализации для любых процессоров обработки файлов.
 *
 * Процессор предоставляет 2 самых важных метода: plan и process
 * Plan - создает запись в таблице обработки (реализации ZfeFiles_Model_Processing_Interface)
 * Process - обновляет запись в таблице обработки
 */
abstract class ZfeFiles_Processor
{
    /**
     * Запись обработки.
     *
     * @var ZfeFiles_Model_Processing_Interface
     */
    protected $processing;

    /**
     * Constructor.
     */
    public function __construct(ZfeFiles_Model_Processing_Interface $item)
    {
        $this->processing = $item;
    }

    /**
     * Получить запись обработки.
     *
     * @throws ZfeFiles_Exception
     */
    public function getProcessing(): ZfeFiles_Model_Processing_Interface
    {
        if ($this->processing === null) {
            throw new ZfeFiles_Exception('Запись обработки не задана.');
        }
        return $this->processing;
    }

    /**
     * Запланировать обработку.
     *
     * Создает запись обработки для файла&
     * Запись на обработку создается при загрузке файла.
     * Не сохраняет запись в БД!
     */
    abstract public function plan(ZfeFiles_Model_File $file): self;

    /**
     * Выполнить обработку.
     *
     * Обновляет запись обработки, созданную в методом plan(), для файла.
     * Обработка осуществляется в фоновом режиме.
     * Не сохраняет запись в БД!
     */
    abstract public function process(ZfeFiles_Loader $loader): self;

    /**
     * Получить описание обработки.
     */
    abstract public function getDescription(): string;

    public function __toString()
    {
        return get_class($this);
    }
}
