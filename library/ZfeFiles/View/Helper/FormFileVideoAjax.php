<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Построитель элемента формы для Ajax-загрузки видео файлов.
 *
 * @property ZFE_View $view
 */
class ZfeFiles_View_Helper_FormFileVideoAjax extends ZfeFiles_View_Helper_FormFileAjax
{
    public function formFileVideoAjax(string $name, $value = null, array $attribs = null): string
    {
        return $this->formFileAjax($name, $value, $attribs);
    }
}
