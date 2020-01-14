<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Schema_Collection implements IteratorAggregate
{
    /**
     * Карта схем коллекции.
     *
     * @var ZfeFiles_Schema_Interface[]
     */
    protected $map = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->map);
    }

    /**
     * Добавить схему в коллекцию.
     */
    public function add(ZfeFiles_Schema_Interface $schema): self
    {
        $code = $schema->getCode();
        if ($this->hasCode($code)) {
            throw new ZfeFiles_Exception("Схема файлов с кодом '{$code}' уже присутствует в коллекции.");
        }

        $this->map[$code] = $schema;
        return $this;
    }

    /**
     * Получить схему из коллекции по коду.
     */
    public function getByCode(string $code): ZfeFiles_Schema_Interface
    {
        if ($this->hasCode($code)) {
            return $this->map[$code];
        }

        throw new ZfeFiles_Exception("Схема файлов с кодом '{$code}' отсутствует в коллекции.");
    }

    /**
     * Удалить схему по коду.
     */
    public function removeByCode(string $code): self
    {
        unset($this->map[$code]);
        return $this;
    }

    /**
     * Проверить наличие схемы с кодом в коллекции.
     */
    public function hasCode(string $code): bool
    {
        return array_key_exists($code, $this->map);
    }
}
