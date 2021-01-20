<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Элемент формы для Ajax-загрузки файлов изображений.
 */
class ZfeFiles_Form_Element_FileAjaxImage extends Zend_Form_Element_Xhtml
{
    /**
     * {@inheritdoc}
     */
    public $helper = 'formFileImageAjax';
}
