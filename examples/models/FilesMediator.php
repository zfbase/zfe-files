<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Пример модели для хранения связи между файлом и управлящей записью при связи много-ко-многим.
 */
class FilesMediator extends BaseFilesMediator implements ZfeFiles_MediatorInterface
{
    private ?ZfeFiles_FileInterface $file = null;
    private ?ZfeFiles_Manageable $item = null;
    private ?ZfeFiles_Schema_Default $schema = null;

    /**
     * {@inheritdoc}
     */
    public function getFile(): ZfeFiles_MediatorInterface
    {
        return $this->File;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem(): ?ZfeFiles_Manageable
    {
        if ($this->item === null) {
            $modelName = $this->model_name;
            $itemId = $this->item_id;
            if ($modelName && $itemId) {
                $this->item = $modelName::find($itemId);
            }
        }

        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        if ($this->schema === null) {
            $modelName = $this->model_name;
            $schemaCode = $this->schema_code;
            if ($modelName && $schemaCode) {
                $this->schema = $modelName::getFileSchemas()->get($schemaCode);
            }
        }

        return $this->schema;
    }
}
