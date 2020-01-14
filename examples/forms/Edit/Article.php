<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class Application_Form_Edit_Article extends ZFE_Form_Edit_AutoGeneration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->addFileAjaxElement('file');
    }
}
