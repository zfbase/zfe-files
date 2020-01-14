<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Элемент формы для Ajax-загрузки файлов.
 */
class ZfeFiles_Form_Element_FileAjax extends Zend_Form_Element_Xhtml
{
    public $helper = 'formFileAjax';
}
