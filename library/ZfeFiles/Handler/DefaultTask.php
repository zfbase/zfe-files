<?php

class ZfeFiles_Handler_DefaultTask implements ZfeFiles_Handler_Interface
{
    /**
     * Код отложенной задачи.
     */
    protected string $taskCode;

    /**
     * Экземпляр менеджера отложенных задач.
     */
    protected ZFE_Tasks_Manager $taskManager;

    public function __construct(string $taskCode)
    {
        $this->taskCode = $taskCode;
        $this->taskManager = ZFE_Tasks_Manager::getInstance();
    }

    /**
     * Получить объект для отложенной задачи.
     * 
     * @return ZfeFiles_FileInterface|AbstractRecord
     */
    protected function getItem(ZfeFiles_Agent_Interface $agent): AbstractRecord
    {
        return $agent->getFile();
    }

    /**
     * @inheritDoc
     */
    public function process(ZfeFiles_Agent_Interface $agent, bool $force = false): void
    {
        $this->taskManager->plan($this->taskCode, $this->getItem($agent));
    }

    /**
     * @inheritDoc
     */
    public function isDone(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isDone(): false;
    }

    /**
     * @inheritDoc
     */
    public function isSuccess(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isSuccess(): false;
    }

    /**
     * @inheritDoc
     */
    public function isFailed(ZfeFiles_Agent_Interface $agent): bool
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->isFailed(): false;
    }

    /**
     * @inheritDoc
     */
    public function getError(ZfeFiles_Agent_Interface $agent): ?string
    {
        $task = $this->taskManager->getLastTask($this->taskCode, $this->getItem($agent)->id);
        return $task ? $task->getError() : null;
    }
}