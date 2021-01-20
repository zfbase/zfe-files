<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Простейший файловый контроллер.
 */
class FilesController extends ZfeFiles_Controller_Default
{
    protected static $_modelName = Files::class;
}
