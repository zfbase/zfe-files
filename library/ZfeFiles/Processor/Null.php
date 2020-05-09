<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Процессор который ничего не делает.
 */
class ZfeFiles_Processor_Null implements ZfeFiles_Processor_Interface
{
    /**
     * @inheritDoc
     */
    public function process(ZfeFiles_FileInterface $file): void
    {
    }

    /**
     * @inheritDoc
     */
    public function isPerformed(ZfeFiles_FileInterface $file): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDone(ZfeFiles_FileInterface $file): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(ZfeFiles_FileInterface $file): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isFailed(ZfeFiles_FileInterface $file): bool
    {
        return false;
    }
}