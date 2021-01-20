<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Пример управлящей записи (к которой привязываются файлы и через которую ими управляют).
 */
class Items extends AbstractRecord implements ZfeFiles_Manageable
{
    private static ZfeFiles_Schema_Collection $fileSchemas;

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Exception
     */
    public static function getFileSchemas(): ZfeFiles_Schema_Collection
    {
        if (static::$fileSchemas === null) {
            static::$fileSchemas = new ZfeFiles_Schema_Collection();
            static::$fileSchemas->add(new ZfeFiles_Schema_Default([
                'code' => 'files',
                'title' => 'Приложения',
                'multiple' => true,
            ]));
        }

        return static::$fileSchemas;
    }
}
