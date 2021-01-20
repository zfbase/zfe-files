<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей, хранящих информацию об оригинальных файлах
 * (загруженных пользователем или сгенерированных системой для пользователя).
 *
 * Отличается от ZfeFiles_File_Interface тем, что это только оригинальные файлы,
 * исключая не оригинальные прокси-копии.
 */
interface ZfeFiles_File_OriginInterface extends ZfeFiles_File_Interface
{
    /**
     * Получить менеджера для файлов этого типа.
     */
    public static function getManager(): ZfeFiles_Manager_Interface;
}
