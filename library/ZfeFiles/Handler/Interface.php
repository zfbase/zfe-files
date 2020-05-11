<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс обработчиков файлов.
 */
interface ZfeFiles_Handler_Interface
{
    /**
     * Обработать файл.
     *
     * В методе может как осуществляться непосредственная обработка файла,
     * так и планироваться отложенная обработка.
     */
    public function process(ZfeFiles_Agent_Interface $agent, bool $force = false): void;

    /**
     * Обработка выполнена?
     */
    public function isDone(ZfeFiles_Agent_Interface $agent): bool;

    /**
     * Обработка выполнена успешно?
     */
    public function isSuccess(ZfeFiles_Agent_Interface $agent): bool;

    /**
     * Обработка окончилась с ошибкой?
     */
    public function isFailed(ZfeFiles_Agent_Interface $agent): bool;

    /**
     * Получить ошибки выполнения.
     */
    public function getError(ZfeFiles_Agent_Interface $agent): ?string;
}