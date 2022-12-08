<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Агент файлов, связывающихся с управляющими записями по связи типа много-ко-многим.
 */
class ZfeFiles_Agent_Multiple extends ZfeFiles_Agent_Abstract
{
    /**
     * Медиатор.
     */
    protected ?ZfeFiles_MediatorInterface $mediator = null;

    /**
     * @param ZfeFiles_File_OriginInterface|AbstractRecord   $file
     * @param ZfeFiles_MediatorInterface|AbstractRecord|null $mediator
     */
    public function __construct($file, $mediator = null)
    {
        $this->file = $file;
        $this->mediator = $mediator;
    }

    /**
     * Получить медиатор
     *
     * @return ZfeFiles_MediatorInterface|AbstractRecord|null
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
    public function linkManageableItem(string $code, ZfeFiles_Manageable $item, array $data = [])
    {
        /** @var ZfeFiles_Manager_Multiple $manager */
        $manager = ($this->file)::getManager();
        $this->mediator = $manager->createMediator($this->file, get_class($item), $code, $item->id, $data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unlinkManageableItem()
    {
        if ($this->mediator) {
            $this->mediator->delete();
        }

        return $this;
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
