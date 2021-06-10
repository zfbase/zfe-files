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
     * @throws Zend_Exception
     *
     * @return self
     */
    public function addFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        if (!is_a($this->_modelName, ZfeFiles_Manageable::class, true)) {
            throw new ZfeFiles_Exception("Загрузка файлов невозможна: {$this->_modelName} не реализует интерфейс ZfeFiles_Manageable");
        }

        /** @var ZfeFiles_Schema_Default $schema */
        $schema = ($this->_modelName)::getFileSchemas()->getByCode($id);
        $schemaOptions = [
            'label' => $schema->getTitle(),
            'required' => $schema->getRequired(),
            'multiple' => $schema->getMultiple(),
            'upload_url' => ($schema->getModel())::getManager()->getUploadUrl(),
            'accept' => $schema->getAccept(),
        ];

        $localOptions = [
            'model_name' => $this->_modelName,
            'schema_code' => $id,
        ];

        $options = array_replace_recursive($schemaOptions, $localOptions, $customOptions);

        $map = [
            'image' => 'fileAjaxImage',
            'audio' => 'fileAjaxAudio',
            'video' => 'fileAjaxVideo',
        ];
        $type = isset($options['type'], $map[$options['type']])
            ? $map[$options['type']]
            : 'fileAjax';

        return $this->addElement($type, $elementName ?: $id, $options);
    }

    /**
     * Добавить элемент загрузки картинок по средством Ajax.
     *
     * @throws Zend_Exception
     *
     * @return self
     */
    public function addImageFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        /** @var ZfeFiles_Schema_Image $schema */
        $schema = ($this->_modelName)::getFileSchemas()->getByCode($id);
        $schemaOptions = [];

        $width = $schema->getWidth();
        $height = $schema->getHeight();
        if ($width && $height) {
            $schemaOptions['data-width'] = $width;
            $schemaOptions['data-height'] = $height;
            $schemaOptions['description'] = "Размер (ш×в): {$width}×{$height}px";
        }

        $localOptions = [
            'type' => 'image',
        ];

        $options = array_replace_recursive($schemaOptions, $localOptions, $customOptions);
        return $this->addFileAjaxElement($id, $options, $elementName);
    }

    /**
     * Добавить элемент загрузки звуковых файлов по средством Ajax.
     *
     * @throws Zend_Exception
     *
     * @return self
     */
    public function addAudioFileAjaxElement(string $id, array $customOptions = [], string $elementName = null)
    {
        return $this->addFileAjaxElement($id, ['type' => 'audio'] + $customOptions, $elementName);
    }

    /**
     * Добавить элемент загрузки видео файлов по средством Ajax.
     *
     * @throws Zend_Exception
     *
     * @return self
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
