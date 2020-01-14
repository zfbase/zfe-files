<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

interface ZfeFiles_Uploader_Handler_Interface
{
    /**
     * Загрузить файл.
     *
     * @throw ZfeFiles_Uploader_Exception
     */
    public function upload(): ZfeFiles_Uploader_Result;
}
