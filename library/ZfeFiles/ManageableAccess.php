<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Класс управления записью модели для управления её файлами.
 */
class ZfeFiles_ManageableAccess
{
    /**
     * @var ZfeFiles_Manageable
     */
    protected $record;

    public function setRecord(ZfeFiles_Manageable $record): self
    {
        $this->record = $record;
        return $this;
    }

    public function getRecord(): ZfeFiles_Manageable
    {
        if (empty($this->record)) {
            throw new ZfeFiles_Exception('Объект не задан');
        }
        return $this->record;
    }

    public function hasRecord(): bool
    {
        return !!$this->record;
    }
}
