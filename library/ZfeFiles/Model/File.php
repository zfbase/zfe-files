<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Базовая модель файлов.
 */
abstract class ZfeFiles_Model_File extends BaseFiles
{
    public static $nameSingular = 'Файл';
    public static $namePlural = 'Файлы';

    /**
     * Получить запись, для который был загружен данный файл.
     */
    public function getManageableItem(): ?ZfeFiles_Manageable
    {
        if (!$this->model_name || !$this->item_id) {
            return null;
        }

        $q = ZFE_Query::create()
            ->from($this->model_name)
            ->where('id = ?', $this->item_id)
            ->setHard(true)
        ;
        return $q->fetchOne() ?: null;
    }

    /**
     * Возвращает описание допустимых обработок для файлов.
     */
    public function getProcessings(): ZfeFiles_Processor_Mapping
    {
        return new ZfeFiles_Processor_Mapping($this);
    }
}
