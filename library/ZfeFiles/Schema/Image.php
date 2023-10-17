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
     * {@inheritdoc}
     */
    protected string $formHelper = 'addImageFileAjaxElement';

    /**
     * Ширина изображения.
     */
    protected ?int $width = null;

    /**
     * Высота изображения.
     */
    protected ?int $height = null;

    /**
     * Поддержка редактирования атрибута Alt
     */
    protected bool $alt = false;

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function __construct(array $options = [])
    {
        $this->setAccept('image/*');

        $this->optionKeys[] = 'width';
        $this->optionKeys[] = 'height';
        if (isset($options['alt']) && $options['alt'] === true) {
            $this->alt = true;
            $this->optionKeys[] = 'alt';
        }
        parent::__construct($options);
    }

    /**
     * Установить ширину изображения.
     */
    public function setWidth(int $width): self
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
    public function setHeight(int $height): self
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
