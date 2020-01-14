<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей, к которым могут привязываться файлы.
 */
interface ZfeFiles_Manageable
{
    /**
     * Получить коллекцию схем привязываемых файлов.
     */
    public static function getFileSchemas(): ZfeFiles_Schema_Collection;
}
