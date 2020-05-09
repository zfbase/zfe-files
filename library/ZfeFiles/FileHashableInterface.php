<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей с особенностями хеширования.
 */
interface ZfeFiles_FileHashableInterface
{
    /**
     * Рассчитать хеш-сумму файла.
     */
    public function hash(bool $autoSave): void;
}
