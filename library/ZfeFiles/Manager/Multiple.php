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
     * @inheritDoc
     */
    protected string $agentClassName = ZfeFiles_Agent_Multiple::class;

    /**
     * @inheritDoc
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

        $hash = $this->hash($data['tempPath']);

        /** @var ZfeFiles_File_OriginInterface|Files $file */
        $file = ($this->fileModelName)::findOneBy('hash', $hash);
        if (!$file) {
            try {
                $file = new $this->fileModelName;
                $file->title = ZfeFiles_Helpers::cutExtension($data['fileName']);
                $file->extension = $data['fileExt'] ?? ZfeFiles_Helpers::extensionFromFilename($data['fileName']);
                $file->size = $data['fileSize'] ?? filesize($data['tempPath']);
                $file->hash = $hash;
                $file->save();
            } catch (Exception $ex) {
                throw new ZfeFiles_Exception('Не удалось сохранить файл', null, $ex);
            }

            $this->move($file, $data['tempPath']);
        }

        $modelName = $data['modelName'] ?? null;
        $schemaCode = $data['schemaCode'] ?? null;
        $itemId = $data['itemId'] ?? null;
        if ($modelName && $schemaCode) {
            if ($modelName && !is_a($modelName, ZfeFiles_Manageable::class, true)) {
                throw new ZfeFiles_Exception('Указанная модель управляющей записи не реализует интерфейс ZfeFiles_Manageable');
            }

            try {
                $mediator = $this->createMediator($file, $modelName, $schemaCode, $itemId);
            } catch(Exception $ex) {
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
     */
    protected function createMediator(
        ZfeFiles_File_OriginInterface $file,
        string $modelName,
        string $schemaCode,
        ?int $itemId = null,
        array $data = [],
        bool $autoSave = true
    ): ?ZfeFiles_MediatorInterface
    {
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
     * @inheritDoc
     * @throws Doctrine_Query_Exception
     */
    public function updateForSchema(
        ZfeFiles_Manageable $item,
        string $schemaCode,
        array $rows,
        bool $process = true
    ): void
    {
        $modelName = get_class($item);
        $ids = $this->extractIds($rows);
        $data = $this->extractData($rows);

        // Удаляем медиаторы для устаревших связей
        $qDelete = ZFE_Query::create()
            ->delete()
            ->from($this->mediatorModelName)
            ->where('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
            // ->andWhereNotIn($this->mediatorFileField, $ids)
        ;
        $qDelete->execute();

        // Создаем медиаторы для новых связей
        $qInsert = ZFE_Query::create()
            ->select('*')
            ->from($this->fileModelName)
            ->whereIn('id', $ids)
        ;
        $files = $qInsert->execute();
        foreach($files as $file) {
            $mediator = $this->createMediator(
                $file,
                $modelName,
                $schemaCode,
                $item->id,
                $data[$file->id]
            );

            if ($process) {
                $this->createAgent($mediator->getFile(), $mediator)->process();
            }
        }
    }

    /**
     * @inheritDoc
     * @throws Doctrine_Query_Exception
     */
    public function getAgentsBySchema(ZfeFiles_Manageable $item, string $schemaCode): array
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from($this->mediatorModelName . ' x')
            ->addFrom('x.' . $this->mediatorFileRelation)
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
        ;
        $agents = [];
        foreach ($q->execute() as $mediator) {
            $agents[] = $this->createAgent($mediator->getFile(), $mediator);
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
     */
    public function createAgent(
        ?ZfeFiles_File_OriginInterface $file = null,
        ?ZfeFiles_MediatorInterface $mediator = null
    ): ?ZfeFiles_Agent_Interface
    {
        return $file
            ? new $this->agentClassName($file, $mediator)
            : null;
    }
}
