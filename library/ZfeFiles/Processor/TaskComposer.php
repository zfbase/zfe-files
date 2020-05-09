<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный процессор для обработки файла в отложенных задачах.
 */
class ZfeFiles_Processor_TaskComposer implements ZfeFiles_Processor_Interface
{
    /**
     * Менеджер отложенных задач.
     */
    protected ZFE_Tasks_Manager $manager;

    /**
     * Классы исполнителей задач.
     *
     * @var string[]
     */
    protected array $performerCodes;

    /**
     * @param string[] $performerCodes коды исполнителей задач
     */
    public function __construct(array $performerCodes)
    {
        $this->manager = ZFE_Tasks_Manager::getInstance();
        $this->performerCodes = $performerCodes;
    }

    /**
     * @inheritDoc
     * @throws ZFE_Tasks_Exception
     */
    public function process(ZfeFiles_FileInterface $file): void
    {
        foreach ($this->performerCodes as $performerCode) {
            $this->manager->plan($performerCode, $file);
        }
    }

    /**
     * @inheritDoc
     */
    public function isPerformed(ZfeFiles_FileInterface $file): bool
    {
        foreach ($this->performerCodes as $performerCode) {
            $task = $this->manager->getLast($performerCode, $file);
            if ($task->isPerformed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isDone(ZfeFiles_FileInterface $file): bool
    {
        foreach ($this->performerCodes as $performerCode) {
            $task = $this->manager->getLast($performerCode, $file);
            if (!$task->isDone()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(ZfeFiles_FileInterface $file): bool
    {
        foreach ($this->performerCodes as $performerCode) {
            $task = $this->manager->getLast($performerCode, $file);
            if (!$task->isSuccess()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isFailed(ZfeFiles_FileInterface $file): bool
    {
        foreach ($this->performerCodes as $performerCode) {
            $task = $this->manager->getLast($performerCode, $file);
            if ($task->isFailed()) {
                return true;
            }
        }

        return false;
    }
}