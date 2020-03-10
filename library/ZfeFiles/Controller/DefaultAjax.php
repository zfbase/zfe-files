<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный контроллер для управления файлами посредством AJAX.
 * Единственный не Ajax-метод – lost.
 */
abstract class ZfeFiles_Controller_DefaultAjax extends Controller_Abstract
{
    use ZfeFiles_Controller_DefaultAjaxTrait;

    protected static $_modelName;
}
