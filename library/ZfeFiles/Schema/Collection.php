<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Schema_Collection implements IteratorAggregate
{
    /**
     * Карта схем коллекции.
     *
     * @var ZfeFiles_Schema_Default[]
     */
    protected array $map = [];

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->map);
    }

    /**
     * Добавить схему в коллекцию.
     *
     * @throws ZfeFiles_Exception
     */
    public function add(ZfeFiles_Schema_Default $schema): ZfeFiles_Schema_Collection
    {
        $code = $schema->getCode();
        if ($this->codeExists($code)) {
            throw new ZfeFiles_Exception("Схема файлов с кодом '{$code}' уже присутствует в коллекции.");
        }

        $this->map[$code] = $schema;
        return $this;
    }

    /**
     * Получить схему из коллекции по коду.
     *
     * @throws ZfeFiles_Exception
     */
    public function getByCode(string $code): ZfeFiles_Schema_Default
    {
        if ($this->codeExists($code)) {
            return $this->map[$code];
        }

        throw new ZfeFiles_Exception("Схема файлов с кодом '{$code}' отсутствует в коллекции.");
    }

    /**
     * Удалить схему по коду.
     */
    public function removeByCode(string $code): ZfeFiles_Schema_Collection
    {
        unset($this->map[$code]);
        return $this;
    }

    /**
     * Проверить наличие схемы с кодом в коллекции.
     */
    public function codeExists(string $code): bool
    {
        return array_key_exists($code, $this->map);
    }
}
