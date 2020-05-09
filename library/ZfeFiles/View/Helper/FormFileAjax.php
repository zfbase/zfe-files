<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Построитель элемента формы для загрузки файлов Ajax.
 *
 * @property ZFE_View $view
 */
class ZfeFiles_View_Helper_FormFileAjax extends Zend_View_Helper_FormElement
{
    /**
     * Render.
     *
     * @param mixed $value
     * @todo Придумать как на вход получать экземпляр ZfeFiles_FileInterface
     */
    public function formFileAjax(string $name, $value = null, array $attribs = null): string
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info);

        $attribs['data-name'] = $name;
        $attribs['class'] = 'zfe-files-ajax' . (empty($attribs['class']) ? '' : ' ' . $attribs['class']);
        $this->modifyAttributes($attribs);

        $inputs = [];
        $previews = [];
        if (is_array($value)) {
            $i = 0;
            foreach ($value as $i => $file) {
                if (!is_array($file)) {
                    // @todo load file
                    continue;  // @temp
                }
                $inputs[$i] = $this->input("{$name}[{$i}]", $file);
                $previews[$i] = $this->preview($file);
            }
        }

        if (count($inputs)) {
            $body = implode('', $inputs);
            $body .= $this->view->tag(
                'ul',
                ['class' => 'zfe-files-ajax-static'],
                '<li>' . implode('</li><li>', $previews) . '</li>'
            );
        } else {
            $body = $this->view->tag('span', ['class' => 'empty zfe-files-ajax-static'], 'Файлы не прикреплены.');
        }

        return $this->view->tag('div', $attribs, $body);
    }

    /**
     * Модифицировать атрибуты.
     */
    protected function modifyAttributes(array &$attribs): void
    {
        $map = [
            'accept',
            'model_name',
            'multiple',
            'required',
            'schema_code',
            'type',
            'upload_url',
        ];
        foreach ($map as $key) {
            if (array_key_exists($key, $attribs)) {
                $attribs['data-' . str_replace('_', '-', $key)] = $attribs[$key];
                unset($attribs[$key]);
            }
        }

        if ($attribs['data-multiple']) {
            $attribs['data-multiple'] = 'multiple';
        } else {
            unset($attribs['data-multiple']);
        }
    }

    /**
     * Собрать input-тег.
     */
    protected function input(string $name, array $file): string
    {
        $attrs = [
            'type' => 'hidden',
            'name' => $name,
            'value' => $file['id'],
        ];
        foreach ($file as $key => $value) {
            if ($key !== 'id') {
                $dashedKey = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $key));
                $attrs['data-' . $dashedKey] = $value;
            }
        }
        return $this->view->tag('input', $attrs);
    }

    /**
     * Собрать превьюшку файла.
     */
    protected function preview(array $file): string
    {
        return $this->view->tag(
            'a',
            ['href' => $file['downloadUrl']],
            '<span class="glyphicon glyphicon-file"></span> ' . $file['name']
        );
    }
}
