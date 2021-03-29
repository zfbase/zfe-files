<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Задача обработки файлов.
 */
class ZfeFiles_Handler_DefaultTask implements ZfeFiles_Handler_Interface
{
    /**
     * Код отложенной задачи.
     */
    protected string $taskCode;

    /**
     * Приоритет отложенной задачи.
     */
    protected int $taskPriority;

    /**
     * Экземпляр менеджера отложенных задач.
     */
    protected ZFE_Tasks_Manager $taskManager;

    public function __construct(string $taskCode, int $priority = 0)
    {
        $this->taskCode = $taskCode;
        $this->taskPriority = $priority;

        $this->taskManager = ZFE_Tasks_Manager::getInstance();
    }

    /**
     * Получить объект для отложенной задачи.
     *
     * @return ZfeFiles_File_OriginInterface|AbstractRecord
     */
    protected function getItem(ZfeFiles_Agent_Interface $agent)
    {
        return $agent->getFile();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ZFE_Tasks_Exception
     */
    public function process(ZfeFiles_Agent_Interface $agent, bool $force = false): void
    {
        $this->taskManager->plan(
            $this->taskCode,
            $this->getItem($agent),
            null,
            $this->taskPriority
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isDone(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isDone() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isSuccess() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isFailed() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getError(ZfeFiles_Agent_Interface $agent): ?string
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->getError() : null;
    }
}
