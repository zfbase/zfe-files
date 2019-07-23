<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

class ZfeFiles_PathMapper
{
    const KEY_MAPPED_PATH = 'tmp_path';

    /**
     * @var ZfeFiles_Model_File
     */
    protected $file;

    /**
     * Constructor.
     */
    public function __construct(ZfeFiles_Model_File $file)
    {
        $this->file = $file;
    }

    public function map(string $path): self
    {
        $this->file->mapValue(static::KEY_MAPPED_PATH, $path);
        return $this;
    }

    public function isMapped(): bool
    {
        return $this->file->hasMappedValue(static::KEY_MAPPED_PATH);
    }

    /**
     * @throws Doctrine_Record_Exception
     */
    public function getMapped(): string
    {
        return $this->file->get(static::KEY_MAPPED_PATH);
    }
}
