<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Описание допустимых обработок для записи файла.
 */
class ZfeFiles_Processor_Mapping extends ZfeFiles_LoadableAccess implements IteratorAggregate
{
    /**
     * @var array
     */
    protected $map = [];

    public function __construct(ZfeFiles_Model_File $file)
    {
        $this->setRecord($file);
    }

    /**
     * @throws ZfeFiles_Exception
     */
    public function add(string $processingModelName, bool $refresh = false): self
    {
        $model = new $processingModelName;
        if (!($model instanceof ZfeFiles_Model_Processing_Interface) || !($model instanceof Doctrine_Record)) {
            $message = sprintf(
                'Модель обработки %s должна реализовывать интерфейс %s',
                $processingModelName,
                ZfeFiles_Model_Processing_Interface::class
            );
            throw new ZfeFiles_Exception($message, 10);
        }

        /** @var Doctrine_Record $record */
        $record = $this->getRecord();
        if (!$record->hasRelation($processingModelName)) {
            $message = sprintf(
                'Модель файла должна иметь связь с моделью обработки %s',
                $processingModelName
            );
            throw new ZfeFiles_Exception($message, 20);
        }

        if ($refresh) {
            $record->refreshRelated($processingModelName);
        }

        $this->map[$processingModelName] = $record->get($processingModelName);
        return $this;
    }

    public function get(string $modelName): ?Doctrine_Collection
    {
        if (array_key_exists($modelName, $this->map)) {
            return $this->map[$modelName];
        }
        return null;
    }

    /**
     * @return IteratorIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }
}
