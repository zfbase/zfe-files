<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс агентов файлов, соответствующих ровно одной управляющей записи.
 */
class ZfeFiles_Agent_Mono extends ZfeFiles_Agent_Abstract
{
    /**
     * Управляющая запись.
     */
    protected ?ZfeFiles_Manageable $item = null;

    /**
     * Схема файла.
     */
    protected ?ZfeFiles_Schema_Default $schema = null;

    public function __construct(ZfeFiles_File_OriginInterface $file)
    {
        $this->file = $file;

        if ($file->model_name && $file->schema_code) {
            $this->schema = ($file->model_name)::getFileSchemas()->getByCode($file->schema_code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getManageableItem(): ?ZfeFiles_Manageable
    {
        if (!$this->item && $this->file->model_name && $this->file->item_id) {
            $this->item = ($this->file->model_name)::find($this->file->item_id);
        }
        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->schema;
    }
}
