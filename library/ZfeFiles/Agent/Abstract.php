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
     * @inheritDoc
     */
    public function process(bool $force = false): void
    {
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getFile(): ZfeFiles_File_OriginInterface
    {
        return $this->file;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        if ($this->file instanceof ZfeFiles_File_CustomFilenameInterface) {
            return $this->file->getFilename();
        }

        return $this->file->title . ($this->file->extension ? ".{$this->file->extension}" : '');
    }

    /**
     * @inheritDoc
     */
    public function isAllow(string $privilege): bool
    {
        return true;
    }
}
