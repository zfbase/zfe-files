<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс менеджеров файлов.
 */
interface ZfeFiles_Manager_Interface
{
    /**
     * Получить адрес для загрузки.
     */
    public function getUploadUrl(): string;

    /**
     * Зарегистрировать новый файл по метаданным.
     *
     * @param array $data {
     *     @var string|null $modelName  модель управляющей записи
     *     @var int|null    $itemId     ID управляющей записи
     *     @var string|null $schemaCode код схемы
     *     @var string      $fileName   название файла
     *     @var string|null $fileExt    расширение файла
     *     @var int|null    $fileSize   размер файла в байтах
     *     @var string      $tempPath   временный путь до файла
     * }
     * @return ZfeFiles_Agent_Interface Агент зарегистрированного файла.
     */
    public function factory(array $data): ZfeFiles_Agent_Interface;

    /**
     * Получить общий обработчик для всех файлов менеджера.
     *
     * @return ZfeFiles_Processor_Interface|null
     */
    public function getHandler(): ?ZfeFiles_Handler_Interface;

    /**
     * Обновить данные агентов в БД.
     *
     * @param ZfeFiles_Agent_Interface[] $agents
     */
    public function updateAgents(
        ZfeFiles_Manageable $item,
        ZfeFiles_Schema_Default $schema,
        array $agents
    ): void;

    /**
     * Выполнить обработчики для прикрепленных файлов.
     *
     * @param bool $force Обновить вне зависимости от последнего времени изменения файла или связи.
     */
    public function process(
        string $modelName,
        int $itemId,
        bool $force = false
    ): void;

    /**
     * Создать агенты по данным файлов и связей.
     *
     * @param ZfeFiles_Manageable|ZFE_Model_AbstractRecord $item
     * @return ZfeFiles_Agent_Interface[] 
     */
    public function createAgents(
        array $data,
        string $schemaCode,
        ?ZfeFiles_Manageable $item
    ): array;

    /**
     * Получить агенты соответствующие схеме.
     *
     * @return ZfeFiles_Agent_Interface[]
     * @todo Рассмотреть возможность перехода на генератор с yield
     */
    public function getAgentsBySchema(ZfeFiles_Manageable $item, string $schemaCode): array;

    /**
     * Получить агент по связи файла с управляющей записью.
     */
    public function getAgentByRelation(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Interface;

    /**
     * Получить агент по ID файла.
     */
    public function getAgentByFileId(int $id): ?ZfeFiles_Agent_Interface;

    /**
     * Получить агент по файлу.
     */
    public function getAgentByFile(ZfeFiles_File_OriginInterface $file): ZfeFiles_Agent_Interface;
}
