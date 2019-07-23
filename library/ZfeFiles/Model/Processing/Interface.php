<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Интерфейс для модели, которая хранит данные обработки файлов.
 */
interface ZfeFiles_Model_Processing_Interface
{
    /**
     * Получить процессор, соответствующий модели.
     */
    public function getProcessor(): ZfeFiles_Processor;

    /**
     * Обработка запланирована?
     */
    public function isPlanned(): bool;

    /**
     * Обработка завершена?
     */
    public function isCompleted(): bool;

    /**
     * Связать с файлом.
     */
    public function linkFile(Files $file): self;

    /**
     * Получить связанный файл.
     */
    public function getLinkedFile(): Files;

    /**
     * Установить ошибку.
     *
     * @param int    $code    код ошибки
     * @param string $message сообщение ошибки
     */
    public function setError(int $code, string $message = null): self;

    /**
     * При обработке произошла ошибка?
     */
    public function hasError(): bool;

    /**
     * Получить код ошибки.
     */
    public function getErrorCode(): ?int;

    /**
     * Получить сообщение ошибки.
     */
    public function getErrorMessage(): ?string;
}
