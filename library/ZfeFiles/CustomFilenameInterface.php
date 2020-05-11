<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс для файлов со специфическим хранением имени файла.
 */
interface ZfeFiles_CustomFilenameInterface
{
    /**
     * Имя файла (с расширением).
     */
    public function getFilename(): string;
}
