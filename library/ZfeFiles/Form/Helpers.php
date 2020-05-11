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
     *
     * @return self
     * @throws Zend_Exception
     */
    public function addFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        /** @var ZfeFiles_Schema_Default $schema */
        $schema = ($this->_modelName)::getFileSchemas()->getByCode($id);
        $schemaOptions = [
            'label' => $schema->getTitle(),
            'required' => $schema->getRequired(),
            'multiple' => $schema->getMultiple(),
            'upload_url' => ($schema->getAgent())::getUploadUrl(),
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
     *
     * @return self
     * @throws Zend_Exception
     */
    public function addImageFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        return $this->addFileAjaxElement($id, ['type' => 'image'] + $customOptions, $elementName);
    }

    /**
     * Добавить элемент загрузки звуковых файлов по средством Ajax.
     *
     * @return self
     * @throws Zend_Exception
     */
    public function addAudioFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        return $this->addFileAjaxElement($id, ['type' => 'audio'] + $customOptions, $elementName);
    }

    /**
     * Добавить элемент загрузки видео файлов по средством Ajax.
     *
     * @return self
     * @throws Zend_Exception
     */
    public function addVideoFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        return $this->addFileAjaxElement($id, ['type' => 'video'] + $customOptions, $elementName);
    }

    /**
     * Инициализация префиксов.
     */
    protected function zfeFilesInitializePrefixes(): void
    {
        $this->addPrefixPath(
            'ZfeFiles_Form_Element',
            __DIR__ . '/Element',
            'element'
        );
    }
}
