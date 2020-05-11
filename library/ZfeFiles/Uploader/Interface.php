<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс загрузчика файлов.
 */
interface ZfeFiles_Uploader_Interface
{
    public function __construct(
        string $agentClassName = null,
        ZfeFiles_Uploader_Handler_Interface $uploadHandler = null,
        string $tempRoot = null
    );

    /**
     * Загрузить файл.
     */
    public function upload(array $params = []): ?ZfeFiles_Agent_Interface;
}
