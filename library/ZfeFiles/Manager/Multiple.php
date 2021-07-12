<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Менеджер файлов, связывающихся с одной или более управляющими записями.
 */
class ZfeFiles_Manager_Multiple extends ZfeFiles_Manager_Abstract
{
    /**
     * Модель медиаторов.
     */
    protected string $mediatorModelName = 'FilesMediator';

    /**
     * Название поля медиатора с ID файла.
     */
    protected string $mediatorFileField = 'file_id';

    /**
     * Название связи медиатора с файлом.
     */
    protected string $mediatorFileRelation = 'Files';

    /**
     * Название связи файла с медиатором.
     */
    protected string $fileMediatorRelation = 'FilesMediator';

    /**
     * {@inheritdoc}
     */
    protected string $agentClassName = ZfeFiles_Agent_Multiple::class;

    /**
     * {@inheritdoc}
     */
    protected function setOptions(array $options): void
    {
        parent::setOptions($options);

        if (!empty($options['mediatorModelName'])) {
            $this->mediatorModelName = $options['mediatorModelName'];
        }

        if (!empty($options['mediatorFileField'])) {
            $this->mediatorFileField = $options['mediatorFileField'];
        }

        if (!empty($options['mediatorFileRelation'])) {
            $this->mediatorFileRelation = $options['mediatorFileRelation'];
        }

        if (!empty($options['fileMediatorRelation'])) {
            $this->fileMediatorRelation = $options['fileMediatorRelation'];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     *
     * @return ZfeFiles_Agent_Multiple
     */
    public function factory(array $data, bool $updateFile = false): ZfeFiles_Agent_Interface
    {
        if (empty($data['tempPath'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать путь до него');
        }

        $hash = $data['hash'] ?? $this->hash($data['tempPath']);

        /** @var ZfeFiles_File_OriginInterface|Files $file */
        $file = ($this->fileModelName)::findOneBy('hash', $hash);
        if (!$file || $updateFile) {
            try {
                if (!$file) {
                    $file = new $this->fileModelName;
                }
                $file->title = ZfeFiles_Helpers::cutExtension($data['fileName']);
                $file->extension = $data['fileExt'] ?? ZfeFiles_Helpers::extensionFromFilename($data['fileName']);
                $file->size = $data['fileSize'] ?? filesize($data['tempPath']);
                $file->hash = $hash;
                $file->save();
            } catch (Exception $ex) {
                throw new ZfeFiles_Exception('Не удалось сохранить файл', null, $ex);
            }

            $this->move($file, $data['tempPath']);
            $this->access($file);
        }

        $modelName = $data['modelName'] ?? null;
        $schemaCode = $data['schemaCode'] ?? null;
        $itemId = empty($data['itemId'])
            ? null
            : (((int) $data['itemId']) ?: null);
        if ($modelName && $schemaCode) {
            if ($modelName && !is_a($modelName, ZfeFiles_Manageable::class, true)) {
                throw new ZfeFiles_Exception('Указанная модель управляющей записи не реализует интерфейс ZfeFiles_Manageable');
            }

            try {
                $mediator = $this->createMediator($file, $modelName, $schemaCode, $itemId);
            } catch (Exception $ex) {
                throw new ZfeFiles_Exception('Не удалось связать файл с записью: ' . $ex->getMessage(), null, $ex);
            }

            if ($mediator) {
                return $this->createAgent($file, $mediator);
            }
        }

        return $this->createAgent($file);
    }

    /**
     * Создать медиатор.
     *
     * @param ZfeFiles_File_OriginInterface|AbstractRecord $file
     * @param array                                        $data Параметры связки; например, сведения о кадрировании изображения
     *
     * @return ZfeFiles_MediatorInterface|AbstractRecord
     */
    public function createMediator(
        $file,
        string $modelName,
        string $schemaCode,
        ?int $itemId = null,
        array $data = [],
        bool $autoSave = true
    ) {
        $mediator = new $this->mediatorModelName;
        $mediator->{$this->mediatorFileField} = $file->id;
        $mediator->model_name = $modelName;
        $mediator->item_id = $itemId;
        $mediator->schema_code = $schemaCode;

        if ($autoSave) {
            $mediator->save();
        }

        return $mediator;
    }

    /**
     * {@inheritdoc}
     *
     * @return ZfeFiles_Agent_Multiple[]
     */
    public function createAgents(array $data, string $schemaCode, $item): array
    {
        $agents = [];

        $modelName = get_class($item);
        $fileIds = $this->extractIds($data);
        $data = $this->extractData($data);

        if (!count($fileIds)) {
            return [];
        }

        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->mediatorModelName . ' x INDEXBY ' . $this->mediatorFileField)
            ->addFrom('x.' . $this->mediatorFileRelation)
            ->where('x.model_name = ?', $modelName)
            ->andWhere('x.schema_code = ?', $schemaCode)
            ->andWhere('x.item_id = ?', $item->id)
            ->andWhereIn($this->mediatorFileField, $fileIds)
        ;
        /** @var Doctrine_Collection $mediators */
        $mediators = $q->execute();
        /** @var ZfeFiles_MediatorInterface|AbstractRecord $mediator */
        foreach ($mediators as $mediator) {
            $file = $mediator->getFile();
            $agent = $this->createAgent($file, $mediator);
            $agent->setData($data[$file->id]);
            $agents[] = $agent;
        }

        $newFileIds = array_diff($fileIds, $mediators->getKeys());
        if (count($newFileIds)) {
            $q = ZFE_Query::create()
                ->select('*')
                ->from($this->fileModelName)
                ->whereIn('id', $newFileIds)
            ;
            /** @var Doctrine_Collection $files */
            $files = $q->execute();
            /** @var ZfeFiles_File_OriginInterface|AbstractRecord $file */
            foreach ($files as $file) {
                $mediator = $this->createMediator(
                    $file,
                    $modelName,
                    $schemaCode,
                    $item->id,
                    $data[$file->id],
                    false
                );
                $agents[] = $this->createAgent($file, $mediator);
            }
        }

        return $agents;
    }

    /**
     * {@inheritdoc}
     *
     * @param ZfeFiles_Agent_Multiple[] $agents
     */
    public function updateAgents($item, ZfeFiles_Schema_Default $schema, array $agents): void
    {
        $mediatorIds = [];

        /** @var ZfeFiles_Agent_Multiple[] $agents */
        foreach ($agents as $agent) {
            $agent->save();
            $mediator = $agent->getMediator();
            if ($mediator) {
                $mediatorIds[] = $agent->getMediator()->id;
            }
        }

        // Удаляем медиаторы для устаревших связей
        $qDelete = ZFE_Query::create()
            ->delete()
            ->from($this->mediatorModelName)
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $schema->getCode())
            ->andWhere('item_id = ?', $item->id)
            ->andWhereNotIn('id', $mediatorIds + [0])
        ;
        $qDelete->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function process(string $modelName, int $itemId, string $schemaCode, bool $force = false): void
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->mediatorModelName . ' x')
            ->addFrom('x.' . $this->mediatorFileRelation)
            ->where('x.model_name = ?', $modelName)
            ->andWhere('x.schema_code = ?', $schemaCode)
            ->andWhere('x.item_id = ?', $itemId)
        ;
        $mediators = $q->execute();
        foreach ($mediators as $mediator) {
            $this->createAgent($mediator->getFile(), $mediator)->process($force);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return ZfeFiles_Agent_Multiple[]
     */
    public function getAgentsBySchema($item, string $schemaCode): array
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->mediatorModelName . ' x')
            ->addFrom('x.' . $this->mediatorFileRelation)
            ->where('x.model_name = ?', get_class($item))
            ->andWhere('x.schema_code = ?', $schemaCode)
            ->andWhere('x.item_id = ?', $item->id)
        ;
        $agents = [];
        /** @var ZfeFiles_MediatorInterface|AbstractRecord $mediator */
        foreach ($q->execute() as $mediator) {
            $agents[] = $this->createAgent($mediator->getFile(), $mediator);
        }
        return $agents;
    }

    /**
     * {@inheritdoc}
     *
     * @return ZfeFiles_Agent_Multiple
     */
    public function getAgentByRelation(
        int $fileId,
        string $modelName,
        string $schemaCode,
        int $itemId
    ): ?ZfeFiles_Agent_Interface {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->mediatorModelName . ' x')
            ->addFrom('x.' . $this->mediatorFileRelation)
            ->where($this->mediatorFileField . ' = ?', $fileId)
            ->andWhere('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $itemId)
        ;
        $mediator = $q->fetchOne();
        if (!$mediator) {
            return null;
        }

        $file = $mediator->getFile();
        if (!$mediator->item_id || !$file) {
            return null;
        }

        return $this->createAgent($file, $mediator);
    }

    /**
     * Создать агент для файла.
     *
     * @return ZfeFiles_Agent_Multiple
     */
    public function createAgent(
        ?ZfeFiles_File_OriginInterface $file = null,
        ?ZfeFiles_MediatorInterface $mediator = null
    ): ?ZfeFiles_Agent_Interface {
        return $file
            ? new $this->agentClassName($file, $mediator)
            : null;
    }
}
