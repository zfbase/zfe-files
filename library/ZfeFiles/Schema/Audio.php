<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка звуковых файлов к записи.
 */
class ZfeFiles_Schema_Audio extends ZfeFiles_Schema_Default
{
    /**
     * {@inheritdoc}
     */
    protected string $formHelper = 'addAudioFileAjaxElement';

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function __construct(array $options = [])
    {
        $this->setAccept('audio/*');

        parent::__construct($options);
    }
}
