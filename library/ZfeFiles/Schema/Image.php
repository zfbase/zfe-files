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
     * Ширина изображения. 
     */
    protected ?int $width = null;

    /**
     * Высота изображения
     */
    protected ?int $height = null;

    /**
     * @inheritDoc
     * @throws ZfeFiles_Schema_Exception
     */
    public function __construct(array $options = [])
    {
        $this->setAccept('image/*');

        $this->optionKeys[] = 'width';
        $this->optionKeys[] = 'height';
        parent::__construct($options);
    }

    /**
     * Установить ширину изображения
     */
    public function setWidth($width): Utils_FilesImage_Schema
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Получить ширину изображения.
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Установить высоту изображения.
     */
    public function setHeight($height): Utils_FilesImage_Schema
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Получить высоту изображения.
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }
}
