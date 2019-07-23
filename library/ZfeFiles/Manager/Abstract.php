<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Менеджер файлов для модели, позволяет осуществить все необходимое для управления файлами модели,
 * начиная с их загрузки и заканчивая их удалением.
 * См. Интерфейс ZfeFiles_Manageable, который определяет, что для модели существует её менеджер,
 * и т.о. для модели можно управлять файлами через её менеджера.
 */
abstract class ZfeFiles_Manager_Abstract extends ZfeFiles_ManageableAccess
{
    /**
     * @var ZfeFiles_Loader
     */
    protected $loader;

    /**
     * @var ZFE_Model_Default_Editors
     */
    protected $user;

    /**
     * @var ZfeFiles_Accessor
     */
    protected $accessor;

    /**
     * @var Zend_Config секция files
     */
    protected $config;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor.
     * 
     * @throws ZfeFiles_Exception
     */
    public function __construct(ZfeFiles_Manageable $record, Zend_Config $config)
    {
        $this->record = $record;
        $this->config = $config;
        $this->loader = new ZfeFiles_Loader($config);
    }

    /**
     * Получить схему привязки файлов.
     */
    abstract public function getFieldsSchemas(): ZfeFiles_Schema_Collection;

    /**
     * Инициализировать контролер управления файлами.
     */
    abstract protected function initAccessor(Zend_Acl $acl, ZFE_Model_Default_Editors $user): ZfeFiles_Accessor;

    /**
     * Инициализировать проверки прав на основные операции с файлами.
     *
     * @throws ZfeFiles_Exception
     */
    public function initAccessControl(Zend_Acl $acl, ZFE_Model_Default_Editors $user): void
    {
        $this->accessor = $this->initAccessor($acl, $user);
        $this->accessor->setRecord($this->getRecord());
    }

    public function getLoader(): ZfeFiles_Loader
    {
        return $this->loader;
    }

    public function getAccessor(): ?ZfeFiles_Accessor
    {
        return $this->accessor;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Сохранить файлы для записи
     * Метод выполняет все операции, обусловленные схемой поля, указанной с помощью typeCode.
     *
     * @param array $tmpPaths пути откуда забрать файлы
     * @param int   $typeCode код схемы файла
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Exception
     * @throws Zend_Exception
     */
    public function manage(array $tmpPaths, int $typeCode): void
    {
        $schemas = $this->getFieldsSchemas();
        $schema = $schemas->get($typeCode);

        $loader = $this->getLoader();

        /** @var ZfeFiles_Processor $processor */
        $processor = $schema->getProcessor();

        // find records of existed files with same types
        $toDeleteColl = null;
        if (!$schema->isMultiple()) {
            // для мультизагрузки не надо затирать файлы, надо добавлять
            $toDeleteColl = $this->findFiles($typeCode);
        }

        $toSaveColl = new Doctrine_Collection(Files::class);
        $processings = [];

        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();

        foreach ($tmpPaths as $tmpPath) {  /** @var Files $file */
            $file = $this->createFile($tmpPath, $typeCode);
            $file->save();  // сохраним сразу, чтобы получить id

            $loader->setRecord($file);
            $file = $loader->upload();  // получили значение в поле path
            $toSaveColl->add($file);  // нужно пересохранить

            if ($processor) {  // если определен процессор для файла
                $processor->plan($file);
                $processings[] = $processor->getProcessing();
            }
        }

        // unit of work

        if ($toDeleteColl && $toDeleteColl->count()) {
            foreach ($toDeleteColl as $item) {
                $loader->setRecord($item)->erase();
            }
            $toDeleteColl->delete();
        }

        $toSaveColl->save();

        if (!empty($processings)) {
            foreach ($processings as $processing) {
                $processing->save();
            }
        }

        $conn->commit();
    }

    /**
     * Создать запись файла.
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Exception
     * @throws Zend_Exception
     */
    protected function createFile(string $path, int $typeCode = 0): Files
    {
        if (!file_exists($path)) {
            throw new ZfeFiles_Exception('Файла ' . $path . ' не существует');
        }
        if (!is_readable($path)) {
            throw new ZfeFiles_Exception('Файл ' . $path . ' не доступен для чтения');
        }

        $file = new Files;
        $file->set('model_name', get_class($this->record));
        $file->set('item_id', $this->record->id);
        $file->set('type', $typeCode);

        $name = mb_substr(mb_strrchr($path, '/'), 1);
        $file->set('title', $name);

        $size = filesize($path);
        $file->set('size', $size);

        $hash = hash_file('crc32', $path) ?? 'empty';
        $file->set('hash', $hash);

        $file->set('ext', mb_strtolower(pathinfo($path, PATHINFO_EXTENSION)));

        $file->set('datetime_created', date('Y-m-d H:i:s'));
        if ($this->user) {
            $file->set('creator_id', $this->user->id);
        }

        $file->set('path', '');

        $mapper = new ZfeFiles_PathMapper($file);
        $mapper->map($path);

        $file->clearRelated();
        return $file;
    }

    /**
     * Получить список агентов по файлам, для указанных схемами полей файлов, если они были загружены.
     *
     * @param bool $byCode группировать агентов в группу по код поля, а не по названию?
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Query_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getAgents(ZfeFiles_Schema_Collection $schemas, bool $byCode = false): array
    {
        $list = [];
        $uploaded = $this->getFiles();
        foreach ($schemas as $schema) {  /** @var ZfeFiles_Schema $schema */
            $key = $byCode ? $schema->getTypeCode() : $schema->getTitle();
            $list[$key] = [];
            $typeCode = $schema->getTypeCode();
            foreach ($uploaded as $file) {  /** @var ZfeFiles_Model_File $file */
                if ($file->type == $typeCode) {
                    $agent = $this->createFileAgent($file);
                    $list[$key][] = $agent;
                }
            }
        }
        return $list;
    }

    /**
     * Проверить наличие файлов по обязательным полям (если такие есть).
     * Возвращает массив с текстами о проблемах (если возникли).
     *
     * @param bool $strict проверить наличие файла в ФС
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Query_Exception
     * @throws Doctrine_Record_Exception
     * @throws Zend_Exception
     */
    public function checkRequired(bool $strict = false): array
    {
        $files = $this->getFiles('type');
        $schemas = $this->getFieldsSchemas();

        $problems = [];
        if ($schemas->hasRequired()) {
            if ($files->count() == 0) {
                $message = 'Необходимо загрузить файлы в обязательные поля';
                $problems[] = $message;
            } else {
                $message = 'Необходимо загрузить файл(ы) в обязательное поле "%s"';
                $loader = $this->getLoader();
                foreach ($schemas->getRequired() as $schema) {  /** @var ZfeFiles_Schema $schema */
                    $file = $files->get($schema->getTypeCode());
                    if ($file && $file->exists()) {
                        if ($strict) {
                            // дополнительно проверяем наличие файла в ФС
                            // $path = $loader->setRecord($file)->getResultPath();
                            $path = $loader->setRecord($file)->absFilePath();
                            if (!file_exists($path)) {
                                $problems[] = sprintf($message, $schema->getTitle());
                            }
                        }

                        // NO PROBLEMO!
                    } else {
                        $problems[] = sprintf($message, $schema->getTitle());
                    }
                }
            }
        }

        return $problems;
    }

    /**
     * Получить коллекцию файлов для данной записи.
     *
     * @deprecated использовать findFiles()!
     * 
     * @throws Doctrine_Query_Exception
     */
    public function getFiles(string $indexBy = 'id'): ?Doctrine_Collection
    {
        $files = $this->findFiles();
        $rows = clone $files;
        $res = [];
        foreach ($rows as $row) {
            $res[$row->{$indexBy}] = $row;
        }
        $rows->setKeyColumn($indexBy);
        $rows->setData($res);

        return $rows;
    }

    /**
     * @param int    $typeCode  код схемы поля
     * @param string $indexBy   поля для индексирования, осторожнее с полем type!
     * 
     * @throws Doctrine_Query_Exception
     */
    public function findFiles(int $typeCode = null, string $indexBy = 'id'): Doctrine_Collection
    {
        $itemId = $this->record->id;
        $modelName = get_class($this->record);

        $q = ZFE_Query::create()
            ->select('x.*')
            ->from(Files::class . ' x INDEXBY id')
            ->where('x.item_id = ?', $itemId)
            ->addWhere('x.model_name = ?', $modelName)
        ;
        if ($typeCode !== null) {
            $q->addWhere('x.type = ?', $typeCode);
        }
        $files = $q->execute();

        if ($indexBy !== 'id') {
            $items = clone $files;
            $result = [];
            foreach ($items as $item) {
                // проблема в случае индексирования по type при наличии мультизагрузочных полей
                // может быть несколько файлов с одинаковым type - они потеряются, останется только последний их них
                $result[$item->{$indexBy}] = $item;
            }
            $items->setKeyColumn($indexBy);
            $items->setData($result);
            return $items;
        }

        return $files;
    }

    /**
     * Возвращает кол-во файлов по типам (если указаны) или всего.
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Query_Exception
     */
    public function getFilesCount(array $types = []): int
    {
        /** @var ZFE_Model_AbstractRecord $record */
        $record = $this->getRecord();
        $q = Doctrine_Query::create()
            ->select('COUNT(*)')
            ->from(Files::class)
            ->where('item_id = ?', $record->id)
        ;
        if (!empty($types)) {
            $q->andWhereIn('type', $types);
        }
        return (int) $q->execute([], Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * Создание агента файла, можно переопределить в дочернем менеджере для модели.
     * Используется для получения списка представителей для файлов модели (getAgents).
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    protected function createFileAgent(ZfeFiles_Model_File $file): ZfeFiles_Agent
    {
        $agent = new ZfeFiles_Agent($file);
        if ($this->accessor) {
            $agent->useAccessor($this->accessor);
        }
        return $agent;
    }

    /**
     * Удалить все файлы из ФС и записи о файлах в БД.
     * Или указать код схемы поля для удаление файлов с нужным типом
     *
     * @param mixed $typeCode Код схемы поля для удаления
     *
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Query_Exception
     * @throws Doctrine_Record_Exception
     * @throws Zend_Exception
     */
    public function purge(int $typeCode = null): void
    {
        $existedFiles = $this->findFiles($typeCode);
        if ($existedFiles->count()) {
            foreach ($existedFiles as $file) {  /** @var ZfeFiles_Model_File $file */
                $this->getLoader()->setRecord($file)->erase();
                $file->clearRelated();
                $file->delete();
            }
        }
    }
}
