<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей, хранящих информацию о файлах.
 */
interface ZfeFiles_FileInterface
{
    /**
     * Получить ссылку для загрузки.
     */
    public static function getUploadUrl(): string;

    /**
     * Получить управляющего расположением файла на диске.
     */
    public function getPathHelper(): ZfeFiles_PathHelper_Abstract;

    /**
     * Получить данные для загрузчика.
     */
    public function getDataForUploader(): array;

    /**
     * Информация о файле сохранена в БД?
     *
     * @see Doctrine_Record::exists()
     */
    public function exists();
}
