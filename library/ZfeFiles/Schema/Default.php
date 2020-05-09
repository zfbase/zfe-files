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
     * Модель файлов.
     */
    protected string $model = 'Files';

    /**
     * Код.
     */
    protected string $code = 'file';

    /**
     * Наименование.
     */
    protected string $title = 'Файл';

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
     * Процессор.
     * 
     * @var ZfeFiles_Processor_Interface|string
     */
    protected $processor = ZfeFiles_Processor_Null::class;

    /**
     * Конструктор.
     *
     * @param array $options {
     *     @var string                       $model     модель файлов
     *     @var string                       $code      код
     *     @var string                       $title     наименование
     *     @var bool                         $required  необходимость прикрепления файла
     *     @var string[]|string              $accept    фильтр по типам файлов
     *     @var bool                         $multiple  возможность прикрепления нескольких файла
     *     @var ZfeFiles_Processor_Interface $processor процессор
     * }
     */
    public function __construct(array $options)
    {
        $keys = ['model', 'code', 'title', 'required', 'accept', 'multiple', 'processor'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $options)) {
                $this->{'set' . ucfirst($key)}($options[$key]);
            }
        }
    }

    /**
     * Установить модель файлов.
     */
    public function setModel(string $model): ZfeFiles_Schema_Default
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Получить модель файлов.
     *
     * @throws Zend_Exception
     */
    public function getModel(): ?string
    {
        if (!$this->model) {
            $this->model = Zend_Registry::get('config')->files->modelName;
        }
        return $this->model;
    }

    /**
     * Установить код.
     */
    public function setCode(string $code): ZfeFiles_Schema_Default
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
    public function setTitle(string $title): ZfeFiles_Schema_Default
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
     * Установить необходимость прикрепления файла.
     */
    public function setRequired(bool $required): ZfeFiles_Schema_Default
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
     * @throws ZfeFiles_Schema_Exception
     */
    public function setAccept($accept = []): ZfeFiles_Schema_Default
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
    public function addAccept(string $accept): ZfeFiles_Schema_Default
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
    public function setMultiple(bool $multiple): ZfeFiles_Schema_Default
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
     * Установить процессор.
     *
     * @param ZfeFiles_Processor_Interface|string экземпляр процессора или название его класса
     * @throws ZfeFiles_Schema_Exception
     */
    public function setProcessor($processor): ZfeFiles_Schema_Default
    {
        if (!is_a($processor, ZfeFiles_Processor_Interface::class, true)) {
            throw new ZfeFiles_Schema_Exception('Процессор должен реализовывать интерфейс ZfeFiles_Processor_Interface');
        }

        $this->processor = $processor;
        return $this;
    }

    /**
     * Получить процессор.
     */
    public function getProcessor(): ?ZfeFiles_Processor_Interface
    {
        if (is_string($this->processor)) {
            $this->processor = new $this->processor;
        }

        return $this->processor;
    }
}
