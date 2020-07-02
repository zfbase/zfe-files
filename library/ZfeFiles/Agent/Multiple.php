<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Интерфейс агентов файлов, связывающихся с управляющими записями по связи типа много-ко-многим.
 */
class ZfeFiles_Agent_Multiple extends ZfeFiles_Agent_Abstract
{
    /**
     * Медиатор.
     */
    protected ?ZfeFiles_MediatorInterface $mediator = null;

    public function __construct(ZfeFiles_File_OriginInterface $file, ?ZfeFiles_MediatorInterface $mediator = null)
    {
        $this->file = $file;
        $this->mediator = $mediator;
    }

    public function getMediator(): ?ZfeFiles_MediatorInterface
    {
        return $this->mediator;
    }

    /**
     * @inheritDoc
     */
    public function getManageableItem(): ?ZfeFiles_Manageable
    {
        return $this->mediator ? $this->mediator->getItem() : null;
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->mediator ? $this->mediator->getSchema() : null;
    }
}
