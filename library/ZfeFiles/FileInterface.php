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
    public function getRealPathHelper(): ZfeFiles_PathHelper_Default;

    /**
     * Получить управляющего расположением файла на диске.
     */
    public function getWebPathHelper(): ZfeFiles_PathHelper_DefaultWeb;

    /**
     * Получить данные для загрузчика.
     */
    public function getDataForUploader(): array;

    /**
     * Получить имя файла для скачивания.
     */
    public function getExportFileName(): string;

    /**
     * Получить имя контроллера управления файлами.
     *
     * @see ZFE_Model_AbstractRecord_Getters::getControllerName()
     * @return string
     */
    public static function getControllerName();

    /**
     * Информация о файле сохранена в БД?
     *
     * @see Doctrine_Record::exists()
     * @return bool
     */
    public function exists();
}
