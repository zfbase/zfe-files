<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Абстрактный агент файлов.
 */
abstract class ZfeFiles_Agent_Abstract implements ZfeFiles_Agent_Interface
{
    /**
     * Файл.
     */
    protected ZfeFiles_File_OriginInterface $file;

    /**
     * {@inheritdoc}
     */
    public function process(bool $force = false): void
    {
        /** @var ZfeFiles_Handler_Interface $managerHandler */
        $managerHandler = ($this->file)::getManager()->getHandler();
        if ($managerHandler) {
            $managerHandler->process($this, $force);
        }

        $schema = $this->getSchema();
        if ($schema) {
            $schemaHandler = $schema->getHandler();
            if ($schemaHandler) {
                $schemaHandler->process($this, $force);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public function getDataForUploader(): array
    {
        $data = [
            'id' => $this->file->id,
            'name' => $this->getFilename(),
        ];

        if ($this->isAllow('download')) {
            $data['downloadUrl'] = $this->file->getWebPathHelper()->getPath();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        if ($this->file instanceof ZfeFiles_File_CustomFilenameInterface) {
            return $this->file->getFilename();
        }

        return $this->file->title . ($this->file->extension ? ".{$this->file->extension}" : '');
    }

    /**
     * {@inheritdoc}
     */
    public function isAllow(string $privilege): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(bool $deep = true): array
    {
        return [
            'file' => $this->getFile()->toArray($deep),
            'item' => $this->getManageableItem() ? $this->getManageableItem()->toArray($deep) : null,
            'schema' => $this->getSchema() ? $this->getSchema()->getOptions() : null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __debugInfo()
    {
        return $this->toArray();
    }
}
