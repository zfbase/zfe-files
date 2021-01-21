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
     * {@inheritdoc}
     */
    protected string $agentClassName = ZfeFiles_Agent_Mono::class;

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     *
     * @return ZfeFiles_Agent_Mono
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
     * {@inheritdoc}
     *
     * @return ZfeFiles_Agent_Mono[]
     */
    public function createAgents(array $data, string $schemaCode, ?ZfeFiles_Manageable $item): array
    {
        $ids = $this->extractIds($data);
        if (!$ids) {
            return [];
        }

        $agents = [];
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->whereIn('id', $ids)
        ;
        $files = $q->execute();
        foreach ($files as $file) {
            $agent = $this->createAgent($file);
            $agent->getFile()->setArray([
                'model_name' => get_class($item),
                'schema_code' => $schemaCode,
                'item_id' => $item->id,
            ]);
            $agents[] = $agent;
        }
        return $agents;
    }

    /**
     * {@inheritdoc}
     *
     * @param ZfeFiles_Agent_Mono[] $agents
     */
    public function updateAgents(
        ZfeFiles_Manageable $item,
        ZfeFiles_Schema_Default $schema,
        array $agents
    ): void {
        $fileIds = [];

        foreach ($agents as $agent) {
            $agent->save();
            $fileIds[] = $agent->getFile()->id;
        }

        // Удаляем ссылки для более не связанных файлов
        $qDelete = ZFE_Query::create()
            ->update($this->fileModelName)
            ->set('item_id', 'NULL')
            ->where('item_id = ?', $item->id)
            ->whereNotIn('id', $fileIds)
        ;
        $qDelete->execute();
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     *
     * @throws Doctrine_Query_Exception
     *
     * @return ZfeFiles_Agent_Mono[]
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
     * {@inheritdoc}
     *
     * @return ZfeFiles_Agent_Mono|null
     */
    public function getAgentByRelation(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Interface {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->where('id = ?', $fileId)
            ->andWhere('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $itemId)
        ;
        $file = $q->fetchOne();
        return $file ? $this->createAgent($file) : null;
    }

    /**
     * Создать агент для файла.
     *
     * @return ZfeFiles_Agent_Mono|null
     */
    public function createAgent(?ZfeFiles_File_OriginInterface $file = null): ?ZfeFiles_Agent_Interface
    {
        return $file
            ? new $this->agentClassName($file)
            : null;
    }
}
