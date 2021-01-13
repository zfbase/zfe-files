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
     *
     * @return ZfeFiles_Schema_Collection<ZfeFiles_Schema_Default>
     */
    public static function getFileSchemas(): ZfeFiles_Schema_Collection;
}
