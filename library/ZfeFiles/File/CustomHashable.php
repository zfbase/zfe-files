<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей с особенностями хеширования.
 */
interface ZfeFiles_File_CustomHashable
{
    /**
     * Рассчитать хеш-сумму файла.
     */
    public static function hash(string $path): void;
}
