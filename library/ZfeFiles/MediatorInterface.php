<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс медиаторов.
 */
interface ZfeFiles_MediatorInterface
{
    /**
     * Получить привязанный файл.
     */
    public function getFile(): ZfeFiles_File_OriginInterface;

    /**
     * Получить управляющую запись.
     */
    public function getItem(): ?ZfeFiles_Manageable;

    /**
     * Получить схему.
     */
    public function getSchema(): ?ZfeFiles_Schema_Default;

    /**
     * Отвязать файл (удалить медиатор).
     */
    public function delete();
}
