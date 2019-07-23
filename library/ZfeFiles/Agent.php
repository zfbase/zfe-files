<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Агент файла.
 *
 * Агент представляет файл:
 * презентация/отображение файла, getName, getSize и т.д.
 * отвечает за доступ к нему с помощью методов isAllowedTo*
 * предоставляет карту возможных и проведенных обработок файла
 *
 * @property int    $id
 * @property int    $creator_id
 * @property int    $datetime_created
 * @property string $title
 * @property string $hash
 * @property int    $size
 * @property int    $type
 * @property string $ext
 * @property string $path
 *
 * @method bool   canDelete()
 * @method string getDeleteUrl()
 * @method bool   canDownload()
 * @method string getDownloadUrl()
 * @method bool   canDownloadAll()
 * @method string getDownloadAllUrl()
 * @method bool   canProcess()
 * @method string getProcessUrl()
 */
class ZfeFiles_Agent
{
    /**
     * @var ZfeFiles_Model_File
     */
    protected $file;

    /**
     * @var ZfeFiles_Accessor
     */
    protected $accessor;

    /**
     * @var ZfeFiles_Icons_Interface
     */
    protected $iconsSet;

    /**
     * @var ZfeFiles_Processor_Mapping|null
     */
    protected $processings;

    /**
     * @var bool
     */
    protected $processHandlyPlan = false;

    /**
     * Constructor.
     *
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     */
    public function __construct(ZfeFiles_Model_File $file)
    {
        $this->file = $file;
        $this->processings = $file->getProcessings();
    }

    /**
     * Определить управление доступом.
     */
    public function useAccessor(ZfeFiles_Accessor $accessor): self
    {
        $this->accessor = $accessor;

        if (!$accessor->hasRecord()) {
            $accessor->setRecord($this->file->getManageableItem());
        }

        return $this;
    }

    /**
     * Получить управление доступом.
     */
    public function getAccessor(): ZfeFiles_Accessor
    {
        if (!$this->accessor) {
            $this->useAccessor(new ZfeFiles_Accessor_Acl(Zend_Registry::get('acl'), new Editors()));
        }

        return $this->accessor;
    }

    /**
     * Определить перечень иконок для файла.
     */
    public function useIconsSet(ZfeFiles_Icons_Interface $set): self
    {
        $this->iconsSet = $set;
        return $this;
    }

    /**
     * Получить перечень иконок.
     */
    public function getIconsSet(): ZfeFiles_Icons_Interface
    {
        if (!$this->iconsSet) {
            $this->useIconsSet(new ZfeFiles_Icons_Bootstrap());
        }

        return $this->iconsSet;
    }

    /**
     * Получить иконку для файла.
     */
    public function getIconClass(): string
    {
        return $this->getIconsSet()->getFor($this->file->ext);
    }

    /**
     * Получить читаемый размер файла.
     */
    public function getSize(): string
    {
        return ZFE_File::humanFileSize(intval($this->file->size));
    }

    /**
     * Получить оформленное название записи файла.
     */
    public function getName(): string
    {
        return $this->file->getTitle();
    }

    /**
     * Файл можно обработать?
     */
    public function isProcessable(): bool
    {
        /** @todo Каждый процессор должен сообщать о наборе расширений, который он способен обрабатывать */
        if (in_array($this->file->ext, ['zip', 'rar', 'tar', 'exe'])) {
            return false;
        }

        return $this->processings !== null;
    }

    /**
     * Получить обработки для файла.
     */
    public function getProcessings(): ZfeFiles_Processor_Mapping
    {
        return $this->processings;
    }

    /**
     * Определить возможность ручного планирования обработки для файла.
     */
    public function switchHandlyProcessing(bool $val): self
    {
        $this->processHandlyPlan = $val;
        return $this;
    }

    /**
     * Можно запланировать обработку вручную?
     */
    public function isHandlyProcessingSwitched(): bool
    {
        return $this->processHandlyPlan;
    }

    public function __call(string $name, array $arguments = [])
    {
        // proxy calls to accessor
        if (mb_strpos($name, 'can') === 0) {
            $accessorMethod = 'isAllowTo' . mb_substr($name, 3);
            return $this->accessor->{$accessorMethod}();
        }

        // strrev('Url') -> lrU
        if (mb_strpos(strrev($name), 'lrU') === 0) {
            return $this->accessor->{$name}($this->file);
        }

        throw new BadFunctionCallException();
    }

    public function __get(string $name)
    {
        // proxy calls to file
        return $this->file->get($name);
    }
}
