<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Базовая схема привязка файлов к записи.
 */
class ZfeFiles_Schema_Default implements ZfeFiles_Schema_Interface
{
    /**
     * Модель файлов.
     *
     * @var string
     */
    protected $model;

    /**
     * Код.
     *
     * @var string
     */
    protected $code;

    /**
     * Наименование.
     *
     * @var string
     */
    protected $title;

    /**
     * Необходимость прикрепления файла.
     *
     * @var bool
     */
    protected $required = false;

    /**
     * Фильтр по типам файлов.
     *
     * @var array<string>
     */
    protected $accept = [];

    /**
     * Возможность прикрепления нескольких файла.
     *
     * @var bool
     */
    protected $multiple = false;

    /**
     * Процессор.
     *
     * @var ZfeFiles_Processor_Interface
     */
    protected $processor;

    /**
     * Обработчики.
     * 
     * @var array<ZfeFiles_Processor_Handle_Abstract>|ZfeFiles_Processor_Handle_Abstract[] 
     */
    protected $handlers;

    /** @inheritDoc */
    public function __construct(array $options)
    {
        $keys = ['model', 'code', 'title', 'required', 'accept', 'multiple', 'processor', 'handlers'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $options)) {
                $this->{'set' . ucfirst($key)}($options[$key]);
            }
        }
    }

    /** @inheritDoc */
    public function setModel(string $model): ZfeFiles_Schema_Interface
    {
        $this->model = $model;
        return $this;
    }

    /** @inheritDoc */
    public function getModel(): ?string
    {
        if (!$this->model) {
            $this->model = Zend_Registry::get('config')->files->modelName;
        }
        return $this->model;
    }

    /** @inheritDoc */
    public function setCode(string $code): ZfeFiles_Schema_Interface
    {
        $this->code = $code;
        return $this;
    }

    /** @inheritDoc */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /** @inheritDoc */
    public function setTitle(string $title): ZfeFiles_Schema_Interface
    {
        $this->title = $title;
        return $this;
    }

    /** @inheritDoc */
    public function getTitle(): string
    {
        if (!$this->title) {
            return $this->multiple ? 'Файлы' : 'Файл';
        }
        return $this->title;
    }

    /** @inheritDoc */
    public function setRequired(bool $required): ZfeFiles_Schema_Interface
    {
        $this->required = $required;
        return $this;
    }

    /** @inheritDoc */
    public function getRequired(): bool
    {
        return $this->required;
    }
    
    /** @inheritDoc */
    public function setAccept($accept): ZfeFiles_Schema_Interface
    {
        if (is_array($accept)) {
            $this->accept = $accept;
        } elseif (is_string($accept)) {
            $this->accept = [$accept];
        } elseif ($accept === null) {
            $this->accept = [];
        } else {
            throw new ZfeFiles_Exception('Не допустимый формат фильтра по типам файлов.');
        }
        
        return $this;
    }

    /** @inheritDoc */
    public function addAccept(string $accept): ZfeFiles_Schema_Interface
    {
        $this->accept[] = $accept;
        return $this;
    }

    /** @inheritDoc */
    public function getAccept(): ?string
    {
        return count($this->accept) ? implode(',', $this->accept) : null;
    }

    /** @inheritDoc */
    public function setMultiple(bool $multiple): ZfeFiles_Schema_Interface
    {
        $this->multiple = $multiple;
        return $this;
    }

    /** @inheritDoc */
    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    /** @inheritDoc */
    public function setProcessor(ZfeFiles_Processor_Interface $processor): ZfeFiles_Schema_Interface
    {
        $this->processor = $processor;
        return $this;
    }

    /** @inheritDoc */
    public function getProcessor(): ZfeFiles_Processor_Interface
    {
        if (!$this->processor) {
            $this->processor = new ZfeFiles_Processor_PriorityAndCritical();
        }
        return $this->processor;
    }

    /** @inheritDoc */
    public function setHandlers(array $handlers): ZfeFiles_Schema_Interface
    {
        $this->handlers = $handlers;
        return $this;
    }

    /** @inheritDoc */
    public function addHandler(ZfeFiles_Processor_Handle_Abstract $handler): ZfeFiles_Schema_Interface
    {
        $this->handlers[] = $handler;
        return $this;
    }
    
    /** @inheritDoc */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}
