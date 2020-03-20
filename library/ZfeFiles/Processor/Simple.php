<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Processor_Simple implements ZfeFiles_Processor_Interface
{
    /**
     * Обработчики.
     * 
     * @var array<ZfeFiles_Processor_Handle_Abstract>|ZfeFiles_Processor_Handle_Abstract[]
     */
    protected $handlers = [];

    /** @inheritDoc */
    public function setHandlers(array $handlers): ZfeFiles_Processor_Interface
    {
        $this->clearHandlers();
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
        return $this;
    }

    public function clearHandlers(): ZfeFiles_Processor_Interface
    {
        $this->handlers = [];
        return $this;
    }

    /** {@inheritdoc} */
    public function addHandler(ZfeFiles_Processor_Handle_Abstract $handler, array $options = []): ZfeFiles_Processor_Interface
    {
        $this->handlers[] = $handler;
        return $this;
    }

    /** {@inheritdoc} */
    public function planFile(ZfeFiles_FileInterface $file): void
    {
        foreach ($this->handlers as $handler) {
            $handler->planFile($file);
        }
    }
}
