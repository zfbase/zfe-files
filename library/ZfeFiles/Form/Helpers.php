<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Трейт форм для подключения элементов формы библиотеки.
 */
trait ZfeFiles_Form_Helpers
{
    /**
     * Добавить элемент загрузки файлов по средством Ajax.
     */
    public function addFileAjaxElement($id, array $customOptions = [], $elementName = null): self
    {
        /** @var ZfeFiles_Schema_Interface $schema */
        $schema = ($this->_modelName)::getFileSchemas()->getByCode($id);
        $schemaOptions = [
            'label' => $schema->getTitle(),
            'required' => $schema->getRequired(),
            'multiple' => $schema->getMultiple(),
            'upload_url' => ($schema->getModel())::getUploadUrl(),
            'accept' => $schema->getAccept(),
        ];

        $localOptions = [
            'model_name' => $this->_modelName,
            'schema_code' => $id,
        ];

        $options = array_replace_recursive($schemaOptions, $localOptions, $customOptions);
        return $this->addElement('fileAjax', $elementName ?: $id, $options);
    }

    /**
     * Добавить элемент загрузки картинок по средством Ajax.
     */
    public function addImageFileAjaxElement($id, array $customOptions = [], $elementName = null): self
    {
        return $this->addFileAjaxElement($id, ['type' => 'image'] + $customOptions, $elementName);
    }

    /**
     * Добавить элемент загрузки звуковых файлов по средством Ajax.
     */
    public function addAudioFileAjaxElement($id, array $customOptions = [], $elementName = null): self
    {
        return $this->addFileAjaxElement($id, ['type' => 'audio'] + $customOptions, $elementName);
    }

    /**
     * Добавить элемент загрузки видео файлов по средством Ajax.
     */
    public function addVideoFileAjaxElement($id, array $customOptions = [], $elementName = null): self
    {
        return $this->addFileAjaxElement($id, ['type' => 'video'] + $customOptions, $elementName);
    }
}
