<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Диспетчер ZFE Files.
 */
class ZfeFiles_Dispatcher
{
    /**
     * Получить соответствующую файлу схему.
     */
    public static function getSchemaForFile(ZfeFiles_FileInterface $file): ?ZfeFiles_Schema_Default
    {
        if ($file->model_name && $file->schema_code) {
            return ($file->model_name)::getFileSchemas()->getByCode($file->schema_code);
        }
        return null;
    }

    /**
     * Получить файлы для записи.
     *
     * @return Doctrine_Collection<ZfeFiles_FileInterface>
     * @throws Doctrine_Query_Exception
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public static function loadFiles(ZfeFiles_Manageable $item, string $code): Doctrine_Collection
    {
        $schema = $item::getFileSchemas()->getByCode($code);
        $q = ZFE_Query::create()
            ->select('*')
            ->from($schema->getModel())
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $code)
            ->andWhere('item_id = ?', $item->id)
        ;
        return $q->execute();
    }

    /**
     * Обновить список привязанных файлов.
     *
     * @throws Doctrine_Query_Exception
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public static function updateFiles(ZfeFiles_Manageable $item, string $code, array $ids): void
    {
        $schema = $item::getFileSchemas()->getByCode($code);

        // Удаляем лишние
        $q = ZFE_Query::create()
            ->delete()
            ->from($schema->getModel())
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $code)
            ->andWhere('item_id = ?', $item->id)
            ->andWhereNotIn('id', $ids)
        ;
        $q->execute();

        // Привязываем новые
        $q = ZFE_Query::create()
            ->update($schema->getModel())
            ->set('item_id', $item->id)
            ->whereIn('id', $ids)
        ;
        $q->execute();
    }
}
