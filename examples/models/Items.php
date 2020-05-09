<?php

class Items extends AbstractRecord implements ZfeFiles_Manageable
{
    private static ZfeFiles_Schema_Collection $fileSchemas;

    /**
     * @inheritDoc
     * @throws ZfeFiles_Exception
     */
    public static function getFileSchemas(): ZfeFiles_Schema_Collection
    {
        if (static::$fileSchemas === null) {
            static::$fileSchemas = new ZfeFiles_Schema_Collection();
            static::$fileSchemas->add(new ZfeFiles_Schema_Default([
                'model' => Files::class,
                'code' => 'files',
                'title' => 'Приложения',
                'multiple' => true,
            ]));
        }

        return static::$fileSchemas;
    }
}
