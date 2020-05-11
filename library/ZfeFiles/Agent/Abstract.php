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
     * Модель файлов.
     */
    protected static string $fileModelName = 'Files';

    /**
     * Файл.
     */
    protected ZfeFiles_FileInterface $file;

    /**
     * Обработчик.
     *
     * @var ZfeFiles_Handler_Interface|string|null
     */
    protected static $handler;

    /**
     * @inheritDoc
     */
    public static function getUploadUrl(): string
    {
        return '/' . (static::$fileModelName)::getControllerName() . '/upload';
    }

    /**
     * Получить хеш-сумму для файла.
     *
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public static function hash(string $path): string
    {
        if (!file_exists($path) || !is_file($path)) {
            throw new ZfeFiles_Exception('Невозможно рассчитать хеш-сумму от файла – Файл не найден');
        }

        if (!is_readable($path)) {
            throw new ZfeFiles_Exception('Невозможно рассчитать хеш-сумму от файла – Файл не доступен для чтения');
        }


        if (is_a(static::$fileModelName, ZfeFiles_FileHashableInterface::class, true)) {
            return (static::$fileModelName)::hash($path);
        }

        return hash_file(Zend_Registry::get('config')->files->hashAlgo ?? 'crc32', $path);
    }

    /**
     * Переместить файл из временного расположения в постоянное.
     *
     * @param ZfeFiles_FileInterface|Files $file
     * @throws ZfeFiles_Exception
     */
    protected static function move(ZfeFiles_FileInterface $file, string $tempPath = null): void
    {
        try {
            $newPath = $file->getRealPathHelper()->getPath();
            ZfeFiles_Helpers::prepareDirectory(dirname($newPath));
            if (rename($tempPath, $newPath)) {
                if ($file->contains('path')) {
                    $file->path = $newPath;
                    $file->save();
                }
            } else {
                throw new ZfeFiles_Uploader_Exception('Не удалось переложить загруженный файл из временной директории');
            }
        } catch (Exception $ex) {
            throw new ZfeFiles_Exception('Не удалось переложить файл из временного на постоянное место хранения', null, $ex);
        }
    }

    /**
     * Получить обработчик агента.
     */
    public static function getHandler(): ?ZfeFiles_Handler_Interface
    {
        if (is_string(static::$handler)) {
            static::$handler = new static::$handler;
        }

        return static::$handler;
    }

    /**
     * @inheritDoc
     */
    public function process(bool $force = false): void
    {
        $agentHandler = static::getHandler();
        if ($agentHandler) {
            $agentHandler->process($this, $force);
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
     */
    public static function loadByFileId(int $id): ?ZfeFiles_Agent_Abstract
    {
        $file = (static::$fileModelName)::find($id);
        return $file ? new static($file) : null;
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
    public function getFile(): ZfeFiles_FileInterface
    {
        return $this->file;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        if ($this->file instanceof ZfeFiles_CustomFilenameInterface) {
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
