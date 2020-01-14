<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка видеофайлов к записи.
 */
class ZfeFiles_Schema_Video extends ZfeFiles_Schema_Default implements ZfeFiles_Schema_Interface
{
    /** @inheritDoc */
    public function __construct(array $options)
    {
        $this->setAccept('video/*');
        parent::__construct($options);
    }
}
