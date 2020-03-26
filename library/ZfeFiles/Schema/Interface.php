<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс схемы привязки файлов к записи.
 * 
 * @todo Добавить фильтры и валидаторы.
 */
interface ZfeFiles_Schema_Interface
{
    /**
     * Конструктор.
     *
     * @param string                       $options[code]      код
     * @param string                       $options[title]     наименование
     * @param bool                         $options[required]  необходимость прикрепления файла
     * @param array<string>|string         $options[accept]    фильтр по типам файлов
     * @param bool                         $options[multiple]  возможность прикрепления нескольких файла
     * @param ZfeFiles_Processor_Interface $options[processor] процессор
     */
    public function __construct(array $options);

    /**
     * Установить модель файлов.
     */
    public function setModel(string $model): ZfeFiles_Schema_Interface;

    /**
     * Получить модель файлов.
     */
    public function getModel(): ?string;

    /**
     * Установить код.
     */
    public function setCode(string $code): ZfeFiles_Schema_Interface;

    /**
     * Получить код.
     */
    public function getCode(): ?string;

    /**
     * Установить наименование.
     */
    public function setTitle(string $title): ZfeFiles_Schema_Interface;

    /**
     * Получить наименование.
     */
    public function getTitle(): string;

    /**
     * Установить необходимость прикрепления файла.
     */
    public function setRequired(bool $required): ZfeFiles_Schema_Interface;

    /**
     * Получить необходимость прикрепления файла.
     */
    public function getRequired(): bool;
    
    /**
     * Установить фильтр по типам файлов.
     * 
     * @param array<string>|string|null $accept
     */
    public function setAccept($accept): ZfeFiles_Schema_Interface;

    /**
     * Добавить фильтр по типам файлов.
     */
    public function addAccept(string $accept): ZfeFiles_Schema_Interface;

    /**
     * Получить фильтр по типам фалов.
     */
    public function getAccept(): ?string;

    /**
     * Установить допустимость прикрепления нескольких файлов.
     */
    public function setMultiple(bool $multiple): ZfeFiles_Schema_Interface;

    /**
     * Получить допустимость прикрепления нескольких файлов.
     */
    public function getMultiple(): bool;

    /**
     * Установить процессор.
     * 
     * @param ZfeFiles_Processor_Interface|string экземпляр процессора или название его класса
     */
    public function setProcessor($processor): ZfeFiles_Schema_Interface;

    /**
     * Получить процессор (если определен).
     */
    public function getProcessor(): ?ZfeFiles_Processor_Interface;

    /**
     * Установить обработчики.
     */
    
    public function setHandlers(array $handlers): ZfeFiles_Schema_Interface;

    /**
     * Добавить обработчик.
     */
    public function addHandler(ZfeFiles_Processor_Handle_Abstract $handler): ZfeFiles_Schema_Interface;
    
    /**
     * Получить обработчики.
     */
    public function getHandlers(): array;
}
