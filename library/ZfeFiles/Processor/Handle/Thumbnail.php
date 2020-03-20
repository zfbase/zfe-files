<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Обработчик для создания прокси-копий для изображений.
 */
class ZfeFiles_Processor_Handle_Thumbnail extends ZfeFiles_Processor_Handle_Abstract
{
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /** @inheritDoc */
    public function getName(): string
    {
        return $this->options['name'] ?: parent::getName();
    }

    /** @inheritDoc */
    public static function addToPlan(ZfeFiles_FileInterface $file, array $options): ZfeFiles_Processor_Process_Interface
    {
        $task = new FilesThumbnail();
        $task->file_id = $file->id;
        $task->options = json_encode($options);
        $task->save();

        return $task;
    }

    /** @inheritDoc */
    public function planFile(ZfeFiles_FileInterface $file): void
    {
        static::addToPlan($file, $this->options);
    }

    /** @inheritDoc */
    public static function progressPlan(): void
    {
        $tasks = FilesThumbnail::findBy('status', FilesThumbnail::STATUS_WAIT);
        foreach ($tasks as $task) {
            $file = $task->Files;
            if (!$file || !$file->exists()) {
                throw new ZfeFiles_Processor_Exception('Не удалось загрузить файл.');
            }

            $handler = new static(json_decode($task->options, true));
            $handler->setFile($file);
            $handler->progress();
        }
    }

    /** @inheritDoc */
    public function progress(): void
    {
        if (!$this->file) {
            throw new ZfeFiles_Processor_Exception('Файл для обработки не указан.');
        }

        $imagick = new Imagick($this->file->path);

        if (isset($this->options['width']) || isset($this->options['height'])) {
            $imagick->thumbnailImage(
                $this->options['width'] ?? 0,
                $this->options['height'] ?? 0);
        }

        if (isset($this->options['format'])) {
            $imagick->setImageFormat($this->options['format']);
        }

        $pathHelper = $this->file->getPathHelper();
        $postfix = $this->options['name'] ?? 'thumbnail';
        $ext = $this->options['ext'] ?? null;
        $thumbPath = $pathHelper->getPathForProxy($postfix, $ext);
        $imagick->writeImage($thumbPath);
        $imagick->destroy();
    }
}
