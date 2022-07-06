<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Агент файлов, соответствующих ровно одной управляющей записи.
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

    /**
     * @param ZfeFiles_File_OriginInterface|AbstractRecord $file
     */
    public function __construct($file)
    {
        $this->file = $file;

        if ($file->model_name && $file->schema_code) {
            $this->schema = ($file->model_name)::getFileSchemas()->getByCode($file->schema_code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getManageableItem()
    {
        if (!$this->item && $this->file->model_name && $this->file->item_id) {
            $this->item = ($this->file->model_name)::find($this->file->item_id);
        }
        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function linkManageableItem(string $code, ZfeFiles_Manageable $item, array $data = [])
    {
        $this->item = $item;

        if ($this->file) {
            $this->file->model_name = get_class($item);
            $this->file->schema_code = $code;
            $this->file->item_id = $item->id;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cloneToManageableItem(ZfeFiles_Manageable $item, string $code = null)
    {
        /** @var ZfeFiles_Manager_Multiple $manager */
        $manager = ($this->file)::getManager();
        $agent = $manager->createAgent($this->getFile());
        $agent->linkManageableItem(
            $code ?: $this->schema->getCode(),
            $item,
            $this->getData(),
        );
        return $agent;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
        $this->getFile()->save();
    }
}
