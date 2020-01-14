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
        foreach ($this->map as $handles) {
            $hasWaiting = false;
            $hasNotReady = false;
            foreach ($handles as $handle) {
                switch ($handle->handle->getStatus()) {
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_WAIT:
                        $hasWaiting = true;
                    break;
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_WORKING:
                        $hasNotReady = true;
                    break;
                    case ZfeFiles_Processor_Handle_Abstract::STATUS_ERROR:
                        if ($handle->critical) {
                            throw new ZfeFiles_Processor_Exception("Обработчик {$handle->handle->getName()} завершился ошибкой.");
                        }
                    break;
                }
            }

            if ($hasWaiting || $hasNotReady) {
                // Если на текущем приоритете еще есть не выполненные обработки дальше не идем.
                continue;
            }
        }
    }
}
