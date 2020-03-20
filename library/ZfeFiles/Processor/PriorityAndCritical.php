<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Processor_PriorityAndCritical implements ZfeFiles_Processor_Interface
{
    /**
     * Карта обработчиков.
     *
     * @var ZfeFiles_Processor_Handle_Abstract[]
     */
    protected $map = [];

    /** @inheritDoc */
    public function setHandlers(array $handlers): ZfeFiles_Processor_Interface
    {
        $this->map = [];
        foreach ($handlers as $handler) {
            if (is_array($handler)) {
                $handler = $handler['handler'] ?? null;
                $options = $handler['options'] ?? [];
            } else {
                $options = [];
            }

            if (!($handler instanceof ZfeFiles_Processor_Handle_Abstract)) {
                throw new ZfeFiles_Processor_Exception('Не поддерживаемый обработчик.');
            }

            $this->addHandler($handler, $options);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param int  $options[priority] приоритет (выполнение от меньшему к большему; по умолчанию – 0)
     * @param bool $options[critical] критичность (если её нет, то ошибка в обработчике не останавливает последующих; по умолчанию есть)
     */
    public function addHandler(ZfeFiles_Processor_Handle_Abstract $handler, array $options = []): ZfeFiles_Processor_Interface
    {
        $priority = array_key_exists('priority', $options) ? $options['priority'] : 0;
        $critical = array_key_exists('critical', $options) ? $options['critical'] : true;

        if (!array_key_exists($priority, $this->map)) {
            $this->map[$priority] = [];
            ksort($this->map);
        }

        $this->map[$priority][] = (object) [
            'handler' => $handler,
            'critical' => $critical,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Не выполненные обработки будут включены в план выполнения в порядке очереди.
     */
    public function handleFile(ZfeFiles_FileInterface $file): void
    {
        krsort($this->map);
        foreach ($this->map as $tasks) {  /** @var array $tasks Обработчики с одинаковым приоритетом */
            $hasWaiting = false;
            $hasNotReady = false;
            foreach ($tasks as $task) {  /** @var array $task Задача содержащая обработчик и признак критичности */
                switch ($task->handler->getStatus()) {
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_WAIT:
                        $hasWaiting = true;
                    break;
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_ERROR:
                        if ($task->critical) {
                            throw new ZfeFiles_Processor_Exception("Обработчик {$task->handler->getName()} завершился ошибкой.");
                        }
                    // break;
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_WORKING:
                        $hasNotReady = true;
                    break;
                }

                ZFE_Debug::console('Выполняется ' . $task->handler->getName());
            }

            if ($hasWaiting || $hasNotReady) {
                // Если на текущем приоритете еще есть не выполненные обработки дальше не идем.
                continue;
            }
        }
    }
}
