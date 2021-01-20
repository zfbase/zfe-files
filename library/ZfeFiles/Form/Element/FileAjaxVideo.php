<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Элемент формы для Ajax-загрузки видео файлов.
 */
class ZfeFiles_Form_Element_FileAjaxVideo extends Zend_Form_Element_Xhtml
{
    /**
     * {@inheritdoc}
     */
    public $helper = 'formFileVideoAjax';
}
