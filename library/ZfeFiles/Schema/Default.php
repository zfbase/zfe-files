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
     * Название класса агента.
     */
    protected string $agent = ZfeFiles_Agent_Mono::class;

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
     * @var ZfeFiles_Handler_Interface|string|null
     */
    protected $handler;

    /**
     * Конструктор.
     *
     * @param array $options {
     *     @var string                       $code      код
     *     @var string                       $title     наименование
     *     @var string                       $agent     название класса агента
     *     @var bool                         $required  необходимость прикрепления файла
     *     @var string[]|string              $accept    фильтр по типам файлов
     *     @var bool                         $multiple  возможность прикрепления нескольких файла
     *     @var ZfeFiles_Processor_Interface $handler   обработчик
     * }
     */
    public function __construct(array $options)
    {
        $keys = ['code', 'title', 'agent', 'required', 'accept', 'multiple', 'handler'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $options)) {
                $this->{'set' . ucfirst($key)}($options[$key]);
            }
        }
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
     * Установить название класса агента.
     *
     * @throws ZfeFiles_Schema_Exception
     */
    public function setAgent(string $agent): ZfeFiles_Schema_Default
    {
        if (!is_a($agent, ZfeFiles_Agent_Interface::class, true)) {
            throw new ZfeFiles_Schema_Exception("Класс $agent не может быть классом агента файла – он не реализует ZfeFiles_Agent_Interface");
        }

        $this->agent = $agent;
        return $this;
    }

    /**
     * Получить название класса агента.
     */
    public function getAgent(): ?string
    {
        return $this->agent;
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
     * @param ZfeFiles_Handler_Interface|string экземпляр процессора или название его класса
     * @throws ZfeFiles_Schema_Exception
     */
    public function setHandler($handler): ZfeFiles_Schema_Default
    {
        if (!is_a($handler, ZfeFiles_Handler_Interface::class, true)) {
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
}
