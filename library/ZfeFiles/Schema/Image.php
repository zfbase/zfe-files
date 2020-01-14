<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка картинок к записи.
 */
class ZfeFiles_Schema_Image extends ZfeFiles_Schema_Default implements ZfeFiles_Schema_Interface
{
    /** @inheritDoc */
    public function __construct(array $options)
    {
        $this->setAccept('image/*');
        parent::__construct($options);
    }
}
