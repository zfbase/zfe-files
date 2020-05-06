<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка картинок к записи.
 */
class ZfeFiles_Schema_Image extends ZfeFiles_Schema_Default
{
    /**
     * @inheritDoc
     * @throws ZfeFiles_Schema_Exception
     */
    public function __construct(array $options)
    {
        $this->setAccept('image/*');

        parent::__construct($options);
    }
}
