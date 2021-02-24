<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс загрузчика файлов по URL.
 */
interface ZfeFiles_Downloader_Interface
{
    /**
     * @param string $fileModel модель файлов
     * @param string $taskCode  код задачи по скачиванию
     * @param string $urlField  поле для указания URL источника
     *
     * Не заданные параметры берутся из конфигурации
     */
    public function __construct(string $fileModel = null, string $taskCode = null, string $urlField = null);

    /**
     * Заказать загрузку файла.
     *
     * @return int идентификатор файла
     */
    public function order(array $params): int;
}
