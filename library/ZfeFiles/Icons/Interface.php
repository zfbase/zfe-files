<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Интерфейс для определителей иконок для файлов.
 */
interface ZfeFiles_Icons_Interface
{
    /**
     * Получить CSS-класс соответствующий расширению файла.
     */
    public function getFor(string $ext): string;
}
