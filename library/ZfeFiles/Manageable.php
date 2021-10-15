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

    /**
     * Добавить агента для записи.
     *
     * Реализуется в трейте ZfeFiles_Model_Injection.
     *
     * @param string $code
     * @param ZfeFiles_Agent_Interface $agent
     *
     * @return ZfeFiles_Manageable
     */
    public function addAgent(string $code, ZfeFiles_Agent_Interface $agent): ZfeFiles_Manageable;
}
