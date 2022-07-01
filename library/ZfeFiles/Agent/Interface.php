<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс агентов файлов.
 *
 * Инициализированный агент знает все о файле
 * и может знать конкретную его привязку к управляющей модели.
 */
interface ZfeFiles_Agent_Interface
{
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
     * Получить файл.
     *
     * @return ZfeFiles_File_OriginInterface|AbstractRecord
     */
    public function getFile();

    /**
     * Получить управляющую запись (к которой привязан файл).
     *
     * @return ZfeFiles_Manageable|AbstractRecord|null
     */
    public function getManageableItem();

    /**
     * Привязать управляющую запись.
     */
    public function linkManageableItem(string $code, ZfeFiles_Manageable $item, array $data = []);

    /**
     * Получить схему.
     */
    public function getSchema(): ?ZfeFiles_Schema_Default;

    /**
     * Получить имя файла с расширением.
     */
    public function getFilename(): string;

    /**
     * Проверить право текущего пользователя на конкретное действие над файлом.
     */
    public function isAllow(string $privilege): bool;

    /**
     * Установить дополнительные данные.
     */
    public function setData(array $data): void;

    /**
     * Получить дополнительные данные.
     */
    public function getData(): array;

    /**
     * Привести к массиву.
     */
    public function toArray(bool $deep = true): array;

    /**
     * Сохранить все данные агента.
     */
    public function save(): void;
}
