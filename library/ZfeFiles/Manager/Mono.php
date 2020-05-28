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
     * @throws Doctrine_Query_Exception
     */
    public function updateForSchema(
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
            ->from($this->fileModelName)
            ->where('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
            ->andWhereNotIn('id', $ids)
        ;
        $q->execute();

        // Привязываем новые
        $q = ZFE_Query::create()
            ->update($this->fileModelName)
            ->set('model_name', '?', $modelName)
            ->set('schema_code', '?', $schemaCode)
            ->set('item_id', '?', $item->id)
            ->whereIn('id', $ids)
        ;
        $q->execute();

        if ($process) {
            $q = ZFE_Query::create()
                ->select('*')
                ->from($this->fileModelName)
                ->whereIn('id', $ids)
            ;
            $files = $q->execute();
            foreach ($files as $file) {
                $this->createAgent($file)->process();
            }
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
