<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартная форма для файлов.
 */
class ZfeFiles_Form_Default_File extends ZFE_Form_Edit_AutoGeneration
{
    /**
     * {@inheritdoc}
     */
    protected $_fieldMethods = [
        'title' => 'addTextElement',
    ];

    /**
     * {@inheritdoc}
     */
    protected $_ignoreFields = [
        'model_name',
        'item_id',
        'schema_code',
        'size',
        'hash',
        'extension',
        'path',
    ];

    /**
     * {@inheritdoc}
     */
    public function populate(array $values)
    {
        parent::populate($values);

        if (!empty($values['extension'])) {
            $this->getElement('title')->setAttrib('addon_append', '.' . $values['extension']);
        }
    }
}
