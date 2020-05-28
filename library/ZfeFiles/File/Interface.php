<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс записей, хранящих информацию о файлах.
 */
interface ZfeFiles_File_Interface
{
    /**
     * Получить управляющего расположением файла на диске.
     */
    public function getRealPathHelper(): ZfeFiles_PathHelper_Default;

    /**
     * Получить управляющего расположением файла на диске.
     */
    public function getWebPathHelper(): ZfeFiles_PathHelper_DefaultWeb;
}
