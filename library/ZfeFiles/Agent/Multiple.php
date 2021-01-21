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

    /**
     * @return ZfeFiles_MediatorInterface|ZFE_Model_AbstractRecord|null
     */
    public function getMediator()
    {
        return $this->mediator;
    }

    /**
     * {@inheritdoc}
     */
    public function getManageableItem()
    {
        return $this->mediator ? $this->mediator->getItem() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): ?ZfeFiles_Schema_Default
    {
        return $this->mediator ? $this->mediator->getSchema() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(bool $deep = true): array
    {
        return parent::toArray($deep) + [
            'mediator' => $this->getMediator() ? $this->getMediator()->toArray($deep) : null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
        if ($this->getFile()) {
            $this->getFile()->save();
        }

        if ($this->getMediator()) {
            $this->getMediator()->save();
        }
    }
}
