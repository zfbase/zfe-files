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

        if ($file->model_name && $file->item_id) {
            $this->item = ($file->model_name)::find($file->item_id);
        }
    }

    /**
     * @inheritDoc
     */
    public function getManageableItem(): ?ZfeFiles_Manageable
    {
        return $this->item;
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->schema;
    }
}
