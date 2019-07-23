<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Схема файла - это описание параметров и уникального кода для загрузки файла/файлов и их разделения между собой по смыслу.
 * Для любой модели, для которой существует менеджер, управляющий ее файлами, может быть определена коллекция схем файлов.
 */
class ZfeFiles_Schema
{
    /**
     * Имя поля формы.
     *
     * @var string
     */
    protected $fieldName = 'file';

    /**
     * Название привязки файлов к записи.
     *
     * @var string
     */
    protected $title = 'Файл';

    /**
     * Код типа привязки.
     *
     * @var integer
     */
    protected $typeCode = 0;

    /**
     * Допускается привязка нескольких файлов?
     *
     * @var boolean
     */
    protected $multiple = false;

    /**
     * Допустимые расширения файлов.
     *
     * @var array|null
     * 
     * Если не указано, ограничений нет.
     * 
     * @example ['doc', 'docx', 'rtf', 'odt', 'txt']
     */
    protected $accept;

    /**
     * Файл обязателен?
     *
     * @var boolean
     */
    protected $required = false;

    /**
     * Описание привязки файлов к записи.
     *
     * @var string
     */
    protected $description;

    /**
     * Запись обработки.
     *
     * @var ZfeFiles_Model_Processing_Interface
     */
    protected $processing;

    /**
     * Указать имя поля формы.
     */
    public function setFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * Получить имя поля формы.
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * Указать названия привязки к записи.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Получить название привязки к записи.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Указать код типа привязки.
     */
    public function setTypeCode(int $typeCode): self
    {
        $this->typeCode = $typeCode;
        return $this;
    }

    /**
     * Получить код типа привязки.
     */
    public function getTypeCode(): int
    {
        return $this->typeCode;
    }

    /**
     * Указать возможность прикреплять несколько файлов.
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Получить возможность прикрепления нескольких файлов.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Указать разрешенные расширения.
     */
    public function setAccept(?array $accept): self
    {
        $this->accept = $accept;
        return $this;
    }

    /**
     * Получить разрешенные расширения.
     */
    public function getAccept(): ?array
    {
        return $this->accept;
    }

    /**
     * Получить разрешенные расширения в строчку.
     * 
     * @throws DomainException
     */
    public function getAcceptString(): string
    {
        if ($this->accept === null) {
            return '*';
        }

        if (count($this->accept) == 0) {
            throw new DomainException("Массив {$this::class}::accept не может быть пустым.");
        }

        return '.' . implode(', .', $this->accept);
    }

    /**
     * Установить обязательность прикрепления файла.
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Получить обязательность прикрепления файла.
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Указать описание привязки файлов к записи.
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Получить описание привязки файлов к записи.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Установить запись обработки.
     */
    public function setProcessing(ZfeFiles_Model_Processing_Interface $processing): self
    {
        $this->processing = $processing;
        return $this;
    }

    /**
     * Получить запись обработки.
     */
    public function getProcessing(): ?ZfeFiles_Model_Processing_Interface
    {
        return $this->processing;
    }
}
