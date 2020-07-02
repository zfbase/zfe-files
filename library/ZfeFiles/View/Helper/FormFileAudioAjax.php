<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Построитель элемента формы для Ajax-загрузки аудио файлов.
 *
 * @property ZFE_View $view
 */
class ZfeFiles_View_Helper_FormFileAudioAjax extends ZfeFiles_View_Helper_FormFileAjax
{
    public function formFileAudioAjax(string $name, $value = null, array $attribs = null): string
    {
        return $this->formFileAjax($name, $value, $attribs);
    }
}
