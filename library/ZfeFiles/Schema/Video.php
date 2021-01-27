<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Схема привязка видео-файлов к записи.
 */
class ZfeFiles_Schema_Video extends ZfeFiles_Schema_Default
{
    /**
     * {@inheritdoc}
     */
    protected string $formHelper = 'addVideoFileAjaxElement';

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function __construct(array $options = [])
    {
        $this->setAccept('video/*');

        parent::__construct($options);
    }
}
