<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс агентов файлов.
 * 
 * Инициализированный агент знает все о файле и конкретной его привязке к управляющей модели.
 */
interface ZfeFiles_Agent_Interface
{
    /**
     * Получить адрес для загрузки.
     */
    public static function getUploadUrl(): string;

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
    public static function factory(array $data): ZfeFiles_Agent_Interface;

    /**
     * Получить обработчик назначенный агенту.
     *
     * @return ZfeFiles_Processor_Interface|null
     */
    public static function getHandler(): ?ZfeFiles_Handler_Interface;

    /**
     * Выполнить все обработки файла (от агента и от схемы).
     *
     * По умолчанию выполняются только те обработки, которые еще не выполнялись.
     *
     * Важно! Обработчик может не проверять изменились ли файл или медиатор после исполнения.
     * После изменения файла или медиатора, которые могут повлиять на результат обработки,
     * стоит принудительно повторять обработки.
     *
     * @param bool $force отменить все обработки и выполнить заново
     */
    public function process(bool $force = false): void;

    /**
     * Получить данные для загрузчика.
     */
    public function getDataForUploader(): array;

    /**
     * Обновить список агентов для схемы.
     *
     * @param int[]|string[] $ids
     */
    public static function updateForSchema(
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
    public static function loadBySchema(ZfeFiles_Manageable $item, string $schemaCode): array;

    /**
     * Получить агент по связи файла с управляющей записью.
     */
    public static function loadWithMediator(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Interface;

    /**
     * Получить агент по ID файла.
     */
    public static function loadByFileId(int $id): ?ZfeFiles_Agent_Interface;

    /**
     * Получить файл.
     *
     * @return ZfeFiles_FileInterface|AbstractRecord
     */
    public function getFile(): ZfeFiles_FileInterface;

    /**
     * Получить управляющую запись (к которой привязан файл).
     *
     * @return ZfeFiles_Manageable|AbstractRecord|null
     */
    public function getManageableItem(): ?ZfeFiles_Manageable;

    /**
     * Получить схему.
     */
    public function getSchema(): ?ZfeFiles_Schema_Default;

    /**
     * Имя файла (с расширением).
     */
    public function getFilename(): string;

    /**
     * Проверить право текущего пользователя на конкретное действие над файлом.
     */
    public function isAllow(string $privilege): bool;
}
