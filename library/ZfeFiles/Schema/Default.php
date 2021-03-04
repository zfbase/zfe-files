<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Базовая схема привязка файлов к записи.
 */
class ZfeFiles_Schema_Default
{
    /**
     * Код.
     */
    protected string $code = 'file';

    /**
     * Наименование.
     */
    protected string $title = 'Файл';

    /**
     * Название модели файлов.
     */
    protected string $model = 'Files';

    /**
     * Необходимость прикрепления файла.
     */
    protected bool $required = false;

    /**
     * Фильтр по типам файлов.
     *
     * @var string[]
     */
    protected array $accept = [];

    /**
     * Возможность прикрепления нескольких файла.
     */
    protected bool $multiple = false;

    /**
     * Рекомендованный помощник элемента формы.
     */
    protected string $formHelper = 'addFileAjaxElement';

    /**
     * Процессор.
     *
     * @var ZfeFiles_Handler_Interface|string|null
     */
    protected $handler;

    /**
     * Скрытность схемы.
     */
    protected $hidden = false;

    /**
     * Ключи опций.
     */
    protected $optionKeys = [
        'code',
        'title',
        'model',
        'required',
        'accept',
        'multiple',
        'formHelper',
        'handler',
        'hidden',
    ];

    public function __construct(array $options = [])
    {
        foreach ($this->optionKeys as $key) {
            if (array_key_exists($key, $options)) {
                $this->{'set' . ucfirst($key)}($options[$key]);
            }
        }
    }

    /**
     * Установить код.
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Получить код.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Установить наименование.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Получить наименование.
     */
    public function getTitle(): string
    {
        if (!$this->title) {
            return $this->multiple ? 'Файлы' : 'Файл';
        }
        return $this->title;
    }

    /**
     * Установить название модели файлов.
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function setModel(string $model): self
    {
        if (!is_a($model, ZfeFiles_File_OriginInterface::class, true)) {
            throw new ZfeFiles_Schema_Exception(
                "Класс ${model} не может быть классом файла – он не реализует ZfeFiles_File_OriginInterface"
            );
        }

        $this->model = $model;
        return $this;
    }

    /**
     * Получить название модели файлов.
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Установить необходимость прикрепления файла.
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Получить необходимость прикрепления файла.
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Установить фильтр по типам файлов.
     *
     * @param string[]|string|null $accept
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function setAccept($accept = []): self
    {
        if (is_array($accept)) {
            $this->accept = $accept;
        } elseif (is_string($accept)) {
            $this->accept = explode(' ', $accept);
        } elseif ($accept === null) {
            $this->accept = [];
        } else {
            throw new ZfeFiles_Schema_Exception('Не допустимый формат фильтра по типам файлов.');
        }

        return $this;
    }

    /**
     * Добавить фильтр по типам файлов.
     */
    public function addAccept(string $accept): self
    {
        $this->accept[] = $accept;
        return $this;
    }

    /**
     * Получить фильтр по типам фалов.
     */
    public function getAccept(): ?string
    {
        return count($this->accept) ? implode(',', $this->accept) : null;
    }

    /**
     * Установить допустимость прикрепления нескольких файлов.
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Получить допустимость прикрепления нескольких файлов.
     */
    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Установить помощник элемента формы.
     */
    public function setFormHelper(string $formHelper): self
    {
        $this->formHelper = $formHelper;
        return $this;
    }

    /**
     * Получить помощник элемента формы.
     */
    public function getFormHelper(): string
    {
        return $this->formHelper;
    }

    /**
     * Установить процессор.
     *
     * @param ZfeFiles_Handler_Interface|string|null $handler экземпляр процессора или название его класса
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function setHandler($handler): self
    {
        if (!is_a($handler, ZfeFiles_Handler_Interface::class, true) && $handler !== null) {
            throw new ZfeFiles_Schema_Exception('Процессор должен реализовывать интерфейс ZfeFiles_Handler_Interface');
        }

        $this->handler = $handler;
        return $this;
    }

    /**
     * Получить процессор.
     */
    public function getHandler(): ?ZfeFiles_Handler_Interface
    {
        if (is_string($this->handler)) {
            $this->handler = new $this->handler;
        }

        return $this->handler;
    }

    /**
     * Установить скрытность схемы.
     */
    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Схема скрытая?
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Получить массив опций.
     */
    public function getOptions(): array
    {
        return array_map(function ($optionKey) {
            return $this->{$optionKey};
        }, $this->optionKeys);
    }
}
