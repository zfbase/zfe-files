<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка звуковых файлов к записи.
 */
class ZfeFiles_Schema_Audio extends ZfeFiles_Schema_Default implements ZfeFiles_Schema_Interface
{
    /** @inheritDoc */
    public function __construct(array $options)
    {
        $this->setAccept('audio/*');
        parent::__construct($options);
    }
}
