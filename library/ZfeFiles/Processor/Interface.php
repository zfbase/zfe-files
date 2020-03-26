<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс для процессоров обработки файлов.
 */
interface ZfeFiles_Processor_Interface
{
    /**
     * Обработать файл.
     * 
     * В методе может как осуществляться непосредственная обработка файла,
     * так и планироваться отложенная обработка.
     */
    public function process(ZfeFiles_FileInterface $file): void;

    /**
     * Обработка производилась?
     */
    public function isPerformed(ZfeFiles_FileInterface $file): bool;

    /**
     * Обработка выполнена успешно?
     */
    public function isCompleted(ZfeFiles_FileInterface $file): bool;

    /**
     * Обработка окончилась с ошибкой?
     */
    public function isFailed(ZfeFiles_FileInterface $file): bool;
}
