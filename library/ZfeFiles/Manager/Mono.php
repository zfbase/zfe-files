<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Менеджер файлов, связывающихся с ровно одной управляющей записью.
 */
class ZfeFiles_Manager_Mono extends ZfeFiles_Manager_Abstract
{
    /**
     * @inheritDoc
     */
    protected string $agentClassName = ZfeFiles_Agent_Mono::class;

    /**
     * @inheritDoc
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public function factory(array $data): ZfeFiles_Agent_Interface
    {
        if (empty($data['tempPath'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать путь до него');
        }

        if (empty($data['fileName'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать его имя');
        }

        try {
            /** @var ZfeFiles_File_OriginInterface|Files $file */
            $file = new $this->fileModelName;
            $file->model_name = $data['modelName'] ?? null;
            $file->item_id = $data['itemId'] ?? null;
            $file->schema_code = $data['schemaCode'] ?? null;
            $file->title = ZfeFiles_Helpers::cutExtension($data['fileName']);
            $file->extension = $data['fileExt'] ?? ZfeFiles_Helpers::extensionFromFilename($data['fileName']);
            $file->size = $data['fileSize'] ?? filesize($data['tempPath']);
            $file->hash = $this->hash($data['tempPath']);
            $file->save();
        } catch (Exception $ex) {
            throw new ZfeFiles_Exception('Не удалось сохранить файл', null, $ex);
        }

        $this->move($file, $data['tempPath']);

        return $this->createAgent($file);
    }

    /**
     * @inheritDoc
     */
    public function createAgents(array $data, string $schemaCode, ?ZfeFiles_Manageable $item): array
    {
        $agents = [];
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->whereIn('id', $this->extractIds($data))
        ;
        $files = $q->execute();
        foreach ($files as $file) {
            $agents[] = $this->createAgent($file);
        }
        return $agents;
    }

    /**
     * @inheritDoc
     */
    public function updateAgents(
        ZfeFiles_Manageable $item,
        ZfeFiles_Schema_Default $schema,
        array $agents
    ): void
    {
        $modelName = get_class($item);
        $schemaCode = $schema->getCode();

        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName . ' INDEXBY id')
            ->where('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
        ;
        $oldFiles = $q->execute();
        $oldIds = $oldFiles->getKeys();
 
        $newIds = array_map(function ($agent) {
            return $agent->getFile()->id;
        }, $agents);

        $toUnlinkIds = array_diff($oldIds, $newIds);
        foreach ($oldFiles as $file) {
            if (in_array($file->id, $toUnlinkIds)) {
                $file->item_id = null;
                $file->save();
            }
        }

        $toLinkIds = array_diff($newIds, $oldIds);
        foreach ($agents as $agent) {
            $file = $agent->getFile();
            if (in_array($file->id, $toLinkIds)) {
                $file->model_name = $modelName;
                $file->schema_code = $schemaCode;
                $file->item_id = $item->id;
                $file->save();
            }
        }
    }

    public function process(string $modelName, int $itemId, bool $force = false): void
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->where('model_name = ?', $modelName)
            ->andWhere('item_id = ?', $itemId)
        ;
        $files = $q->execute();
        foreach ($files as $file) {
            $this->createAgent($file)->process($force);
        }
    }

    /**
     * @inheritDoc
     * @return array<ZfeFiles_Agent_Interface>
     * @throws Doctrine_Query_Exception
     */
    public function getAgentsBySchema(ZfeFiles_Manageable $item, string $schemaCode): array
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
        ;
        $files = $q->execute();
        $agents = [];
        foreach ($files as $file) {
            $agents[] = $this->createAgent($file);
        }
        return $agents;
    }

    /**
     * @inheritDoc
     */
    public function getAgentByRelation(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Interface
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->where('id = ?', $fileId)
            ->andWhere('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $itemId)
        ;
        return $this->createAgent($q->fetchOne());
    }

    /**
     * Создать агент для файла.
     */
    public function createAgent(?ZfeFiles_File_OriginInterface $file = null): ?ZfeFiles_Agent_Interface
    {
        return $file
            ? new $this->agentClassName($file)
            : null;
    }
}
