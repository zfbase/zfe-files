<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс для процессоров обработки файлов.
 * Процессоры инкапсулируют управление обработкой файлов.
 */
interface ZfeFiles_Processor_Interface
{
    /**
     * Установить обработчики.
     */
    public function setHandlers(array $handlers): ZfeFiles_Processor_Interface;

    /**
     * Добавить обработчик.
     *
     * @param ZfeFiles_Processor_Handle_Abstract $handler обработчик
     * @param array                              $options опции исполнения обработчиков
     */
    public function addHandler(ZfeFiles_Processor_Handle_Abstract $handler, array $options = []): ZfeFiles_Processor_Interface;

    /**
     * Отправить на выполнение все обработки по файлу.
     */
    public function handleFile(ZfeFiles_FileInterface $file): void;
}
