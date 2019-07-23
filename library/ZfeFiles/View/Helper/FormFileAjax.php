<?php

class ZfeFiles_View_Helper_FormFileAjax extends Zend_View_Helper_FormElement
{
    public function formFileAjax($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info);

        $attribs['class'] = 'zfe-files-ajax' . (empty($attribs['class']) ? '' : ' ' . $attribs['class']);

        $inputs = [];
        $previews = [];
        if (is_array($value)) {
            $i = 0;
            foreach ($value as $i => $file) {
                $inputs[$i] = $this->_hidden("$name[$i]", $file->id);
                $previews[$i] = $this->_preview($file, $attribs['preview'] ?? null);
            }
        }

        if (count($inputs)) {
            $body = implode('', $inputs) . implode('', $previews);
        } else {
            $body = $this->view->tag('span', ['class' => 'empty'], 'Файлы не прикреплены.');
        }

        return $this->view->tag('div', $attribs, $body);
    }

    protected function _preview(ZfeFiles_Model_File $file, $settings = null)
    {
        return "[{$file->title}]";
    }
}
