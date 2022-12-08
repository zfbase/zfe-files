<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Построитель элемента формы для Ajax-загрузки файлов изображений.
 *
 * @property ZFE_View $view
 */
class ZfeFiles_View_Helper_FormFileImageAjax extends ZfeFiles_View_Helper_FormFileAjax
{
    public function formFileImageAjax(string $name, $value = null, array $attribs = null): string
    {
        return $this->formFileAjax($name, $value, $attribs);
    }

    protected $valueProps = [
        'id',
        'x',
        'y',
        'width',
        'height',
        'scaleX',
        'scaleY',
        'rotate',
    ];

    protected function getInputValue(array $file): string
    {
        $value = [];
        foreach (array_keys($file) as $key) {
            if (in_array($key, $this->valueProps)) {
                $value[$key] = $file[$key];
            }
        }

        return count(array_keys($value)) > 1
            ? json_encode($value)
            : $file['id'];
    }
}
