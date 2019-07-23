<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Коллекция схем (см. ZfeFiles_Schema), про которые знает менеджер файлов.
 */
class ZfeFiles_Schema_Collection implements IteratorAggregate
{
    /**
     * @var array<ZfeFiles_Schema>|ZfeFiles_Schema[]
     */
    protected $map = [];

    protected $required = 0;

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->map);
    }

    protected function checkIsTypeUniq(ZfeFiles_Schema $schema): bool
    {
        return !array_key_exists($schema->getTypeCode(), $this->map);
    }

    public function count(): int
    {
        return count($this->map);
    }

    /**
     * @throws ZfeFiles_Exception
     */
    public function add(ZfeFiles_Schema $schema): self
    {
        $code = $schema->getTypeCode();
        if ($this->checkIsTypeUniq($schema)) {
            $this->map[$code] = $schema;
            if ($schema->isRequired()) {
                $this->required++;
            }
        } else {
            throw new ZfeFiles_Exception("Схема поля файла с кодом {$code} уже присутствует в коллекции");
        }
        return $this;
    }

    public function remove(ZfeFiles_Schema $schema): self
    {
        $code = $schema->getTypeCode();
        return $this->removeByCode($code);
    }

    public function removeByCode(int $code): self
    {
        if (array_key_exists($code, $this->map)) {
            if ($this->map[$code]->isRequired()) {
                $this->required--;
            }
            unset($this->map[$code]);
        }
        return $this;
    }

    /**
     * @throws ZfeFiles_Exception
     */
    public function get(int $typeCode): ZfeFiles_Schema
    {
        if (array_key_exists($typeCode, $this->map)) {
            return $this->map[$typeCode];
        }
        throw new ZfeFiles_Exception("Схемы поля файла с кодом {$typeCode} не найдено");
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Record_Exception
     */
    public function getFor(ZfeFiles_Model_File $file): ZfeFiles_Schema
    {
        return $this->get($file->get('type'));
    }

    public function hasRequired(): bool
    {
        return boolval($this->required);
    }

    /**
     * @throws ZfeFiles_Exception
     */
    public function getRequired(): self
    {
        $subCollection = new self();
        foreach ($this->map as $code => $schema) {  /** @var ZfeFiles_Schema $schema */
            if ($schema->isRequired()) {
                $subCollection->add($schema);
            }
        }
        return $subCollection;
    }
}
