<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный процессор для обработки файла в отложенной задаче.
 */
abstract class ZfeFiles_Processor_WithOneTaskAbstract implements ZfeFiles_Processor_Interface
{
    /**
     * Менеджер отложенных задач.
     *
     * @var ZFE_Tasks_Manager
     */
    protected $manager;

    /**
     * Класс исполнителя отложенных файлов.
     */
    protected $performerClass;

    /**
     * Кэш последних задач.
     */
    protected $tasksCache = [];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->manager = ZFE_Tasks_Manager::getInstance();
    }

    /** @inheritdoc */
    public function process(ZfeFiles_FileInterface $file, array $params = []): void
    {
        if (!is_a($this->performerClass, ZFE_Tasks_Performer::class, true)) {
            throw new ZfeFiles_Processor_Exception('Не указан класс исполнителя задач');
        }

        $this->manager->plan($this->performerClass, $file);
    }

    /** @inheritdoc */
    public function isPerformed(ZfeFiles_FileInterface $file): bool
    {
        return $this->getLastTask($file) !== null;
    }

    /** @inheritdoc */
    public function isCompleted(ZfeFiles_FileInterface $file): bool
    {
        $lastTask = $this->getLastTask($file);
        return $lastTask->state == Tasks::STATE_DONE && !$lastTask->hasErrors();
    }

    /** @inheritdoc */
    public function isFailed(ZfeFiles_FileInterface $file): bool
    {
        $lastTask = $this->getLastTask($file);
        return $lastTask->state == Tasks::STATE_DONE && $lastTask->hasErrors();
    }

    /**
     * Получить последнюю задачу (и закэшировать).
     */
    protected function getLastTask(ZfeFiles_FileInterface $file): ?Tasks
    {
        if (!array_key_exists($file->id, $this->tasksCache)) {
            if (!($this->performerClass instanceof ZFE_Tasks_Performer)) {
                throw new ZfeFiles_Processor_Exception('Не указан класс исполнителя задач');
            }

            $performerCode = $this->performerClass::getCode();
            $this->tasksCache[$file->id] = $this->manager->getLast($performerCode, $file->id);
        }
        return $this->tasksCache[$file->id];
    }
}