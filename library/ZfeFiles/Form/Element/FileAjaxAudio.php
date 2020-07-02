<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Элемент формы для Ajax-загрузки аудио файлов.
 */
class ZfeFiles_Form_Element_FileAjaxAudio extends Zend_Form_Element_Xhtml
{
    public $helper = 'formFileAudioAjax';
}
