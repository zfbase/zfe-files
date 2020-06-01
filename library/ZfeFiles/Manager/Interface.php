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
     * Обновить список файлов для схемы.
     *
     * @param int[]|string[] $ids ID файла или JSON-строка с расширенными данными
     */
    public function updateForSchema(
        ZfeFiles_Manageable $item,
        string $schemaCode,
        array $ids,
        bool $process = true
    ): void;

    /**
     * Получить агенты соответствующие схеме.
     * 
     * @return array<ZfeFiles_Agent_Interface>
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
