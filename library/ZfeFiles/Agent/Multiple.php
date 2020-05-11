<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс агентов файлов, связывающихся с управляющими записями по связи типа много-ко-многим.
 */
class ZfeFiles_Agent_Multiple extends ZfeFiles_Agent_Abstract
{
    /**
     * Модель медиаторов.
     */
    protected static string $mediatorModelName = 'FilesMediator';

    /**
     * Название поля медиатора с ID файла.
     */
    protected static string $mediatorFileField = 'file_id';

    /**
     * Название связи медиатора с файлом.
     */
    protected static string $mediatorFileRelation = 'Files';

    /**
     * Название связи файла с медиатором.
     */
    protected static string $fileMediatorRelation = 'FilesMediator';

    /**
     * Медиатор.
     */
    protected ?ZfeFiles_MediatorInterface $mediator = null;

    /**
     * @inheritDoc
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public static function factory(array $data): ZfeFiles_Agent_Interface
    {
        if (empty($data['tempPath'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать путь до него');
        }
        if (empty($data['fileName'])) {
            throw new ZfeFiles_Exception('Для регистрации файла необходимо указать его имя');
        }

        $hash = static::hash($data['tempPath']);

        /** @var ZfeFiles_FileInterface|Files $file */
        $file = (static::$fileModelName)::findOneBy('hash', $hash);
        if (!$file) {
            try {
                $file = new static::$fileModelName;
                $file->title = ZfeFiles_Helpers::cutExtension($data['fileName']);
                $file->extension = $data['fileExt'] ?? ZfeFiles_Helpers::extensionFromFilename($data['fileName']);
                $file->size = $data['fileSize'] ?? filesize($data['tempPath']);
                $file->hash = $hash;
                $file->save();
            } catch (Exception $ex) {
                throw new ZfeFiles_Exception('Не удалось сохранить файл', null, $ex);
            }

            static::move($file, $data['tempPath']);
        }

        $modelName = $data['modelName'] ?? null;
        $itemId = $data['itemId'] ?? null;
        $schemaCode = $data['schemaCode'] ?? null;
        if ($modelName && ($itemId || $schemaCode)) {
            if ($modelName && !is_a($modelName, ZfeFiles_Manageable::class, true)) {
                throw new ZfeFiles_Exception('Указанная модель управляющей записи не реализует интерфейс ZfeFiles_Manageable');
            }

            try {
                $mediator = static::createMediator(
                    $file,
                    $data['modelName'],
                    $data['schemaCode'],
                    $data['itemId'] ?? null
                );
            } catch(Exception $ex) {
                throw new ZfeFiles_Exception('Не удалось связать файл с записью', null, $ex);
            }

            return new static($file, $mediator);
        }

        return new static($file);
    }

    /**
     * Создать медиатор.
     *
     * @param ZfeFiles_FileInterface|AbstractRecord $file
     */
    protected static function createMediator(
        ZfeFiles_FileInterface $file,
        string $modelName,
        string $schemaCode,
        ?int $itemId = null,
        bool $autoSave = true
    ): ZfeFiles_MediatorInterface
    {
        $mediator = new static::$mediatorModelName;
        $mediator->{static::$mediatorFileField} = $file->id;
        $mediator->model_name = $modelName;
        $mediator->item_id = $itemId;
        $mediator->schema_code = $schemaCode;

        if ($autoSave) {
            $mediator->save();
        }

        return $mediator;
    }

    protected function __construct(ZfeFiles_FileInterface $file, ?ZfeFiles_MediatorInterface $mediator = null)
    {
        $this->file = $file;
        $this->mediator = $mediator;
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

        // Удаляем медиаторы для устаревших связей
        $qDelete = ZFE_Query::create()
            ->delete()
            ->from(static::$mediatorModelName)
            ->where('model_name = ?', $modelName)
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
            ->andWhereNotIn(static::$mediatorFileField, $ids)
        ;
        $qDelete->execute();

        // Создаем медиаторы для новых связей
        $qInsert = ZFE_Query::create()
            ->select('*')
            ->from(static::$fileModelName)
            ->whereIn('id', $ids)
        ;
        $files = $qInsert->execute();
        foreach($files as $file) {
            $mediator = static::createMediator($file, $modelName, $schemaCode, $item->id);

            if ($process) {
                $agent = new static($mediator->getFile(), $mediator);
                $agent->process();
            }
        }
    }

    /**
     * @inheritDoc
     * @throws Doctrine_Query_Exception
     */
    public static function loadBySchema(ZfeFiles_Manageable $item, string $schemaCode): array
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from(static::$mediatorModelName . ' x')
            ->addFrom('x.' . static::$mediatorFileRelation)
            ->where('model_name = ?', get_class($item))
            ->andWhere('schema_code = ?', $schemaCode)
            ->andWhere('item_id = ?', $item->id)
        ;
        $agents = [];
        foreach ($q->execute() as $mediator) {
            $agents[] = new static($mediator->getFile(), $mediator);
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
    ): ?ZfeFiles_Agent_Interface
    {
        $q = ZFE_Query::create()
            ->select('*')
            ->from(static::$mediatorModelName . ' x')
            ->addFrom('x.' . static::$mediatorFileRelation)
            ->where(static::$mediatorFileField . ' = ?', $fileId)
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

        return new static($file, $mediator);
    }

    /**
     * @inheritDoc
     */
    public function getManageableItem(): ?ZfeFiles_Manageable
    {
        return $this->mediator ? $this->mediator->getItem() : null;
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->mediator ? $this->mediator->getSchema() : null;
    }
}
