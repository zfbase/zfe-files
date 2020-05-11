<?php


class FilesMediator extends BaseFilesMediator implements ZfeFiles_MediatorInterface
{
    private ?ZfeFiles_FileInterface $file = null;
    private ?ZfeFiles_Manageable $item = null;
    private ?ZfeFiles_Schema_Default $schema = null;

    /**
     * @inheritDoc
     */
    public function getFile(): ZfeFiles_MediatorInterface
    {
        return $this->File;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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