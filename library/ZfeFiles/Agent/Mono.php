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

    /**
     * @inheritDoc
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public static function factory(array $data): ZfeFiles_Agent_Mono
    {
        if (empty($data['tempPath'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать путь до него');
        }

        if (empty($data['fileName'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать его имя');
        }

        try {
            /** @var ZfeFiles_FileInterface|Files $file */
            $file = new static::$fileModelName;
            $file->model_name = $data['modelName'] ?? null;
            $file->item_id = $data['itemId'] ?? null;
            $file->schema_code = $data['schemaCode'] ?? null;
            $file->title = ZfeFiles_Helpers::cutExtension($data['fileName']);
            $file->extension = $data['fileExt'] ?? ZfeFiles_Helpers::extensionFromFilename($data['fileName']);
            $file->size = $data['fileSize'] ?? filesize($data['tempPath']);
            $file->hash = static::hash($data['tempPath']);
            $file->save();
        } catch (Exception $ex) {
            throw new ZfeFiles_Exception('Не удалось сохранить файл', null, $ex);
        }

        static::move($file, $data['tempPath']);

        return new static($file);
    }

    protected function __construct(ZfeFiles_FileInterface $file)
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
     * @throws Doctrine_Query_Exception
     */
    public static function updateForSchema(
        ZfeFiles_Manageable $item,
        string $schemaCode,
        array $ids,
        bool $process = true
    ): void
    {
        $modelName = get_class($item);

        // Удаляем лишние
        $q = ZFE_Query::create()
            ->delete()
            ->from(static::$fileModelName)
            ->where('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
            ->andWhereNotIn('id', $ids)
        ;
        $q->execute();

        // Привязываем новые
        $q = ZFE_Query::create()
            ->update(static::$fileModelName)
            ->set('model_name', '?', $modelName)
            ->set('schema_code', '?', $schemaCode)
            ->set('item_id', '?', $item->id)
            ->whereIn('id', $ids)
        ;
        $q->execute();

        if ($process) {
            $q = ZFE_Query::create()
                ->select('*')
                ->from(static::$fileModelName)
                ->whereIn('id', $ids)
            ;
            $files = $q->execute();
            foreach ($files as $file) {
                $agent = new static($file);
                $agent->process();
            }
        }
    }

    /**
     * @inheritDoc
     * @return array<ZfeFiles_Agent_Mono>
     * @throws Doctrine_Query_Exception
     */
    public static function loadBySchema(ZfeFiles_Manageable $item, string $schemaCode): array
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from(static::$fileModelName)
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
        ;
        $files = $q->execute();
        $agents = [];
        foreach ($files as $file) {
            $agents[] = new static($file);
        }
        return $agents;
    }

    /**
     * @inheritDoc
     */
    public static function loadWithMediator(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Mono
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from(static::$fileModelName)
            ->where('id = ?', $fileId)
            ->andWhere('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $itemId)
        ;
        $file = $q->fetchOne();
        return $file ? new static($file) : null;
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
