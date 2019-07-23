<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Класс управления записью файла, для которой происходит работа с файловой системой.
 */
abstract class ZfeFiles_LoadableAccess
{
    /**
     * Запись файла.
     *
     * @var ZfeFiles_Model_File
     */
    protected $record;

    /**
     * Установить запись файла.
     */
    public function setRecord(ZfeFiles_Model_File $record): self
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Получить запись файла.
     *
     * @throws ZfeFiles_Exception
     */
    public function getRecord(): ZfeFiles_Model_File
    {
        if (empty($this->record)) {
            throw new ZfeFiles_Exception('Файл не задан.');
        }
        return $this->record;
    }
}
