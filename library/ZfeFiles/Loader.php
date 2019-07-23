<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Класс знает все о том, как надо сохранить и удалять загруженный файл в файловой системе.
 */
final class ZfeFiles_Loader extends ZfeFiles_LoadableAccess
{
    /**
     * @var Zend_Config секция files
     */
    protected $config;

    /**
     * @var string
     */
    protected $dataDir;

    /**
     * @var string
     */
    protected $dataTempDir;

    /**
     * @var string
     */
    protected $method = 'rename';

    /**
     * @var int Число разрядов для разбиения идентификатора
     */
    protected $div = 3;

    /**
     * Constructor.
     *
     * @param Zend_Config $config секция files
     *
     * @throws ZfeFiles_Exception
     */
    public function __construct(Zend_Config $config): void
    {
        if (!empty($config->files)) {
            $config = $config->files;
        }
        $this->config = $config;

        $dataDir = $config->path;
        if (empty($dataDir)) {
            throw new ZfeFiles_Exception('Не задана настройка files.path в конфигурации');
        }
        if (!is_dir($dataDir) || !is_writable($dataDir)) {
            throw new ZfeFiles_Exception($dataDir . ' не существует или не доступна для записи');
        }

        $dataTempDir = $config->tempPath;
        if (empty($dataTempDir)) {
            throw new ZfeFiles_Exception('Не задана настройка files.tempPath в конфигурации');
        }
        if (!is_dir($dataTempDir) || !is_writable($dataTempDir)) {
            throw new ZfeFiles_Exception($dataTempDir . ' не существует или не доступна для записи');
        }

        $this->dataDir = preg_replace('/\/+$/', '', $dataDir);
        $this->dataTempDir = preg_replace('/\/+$/', '', $dataTempDir);
    }

    public function relateFilePath(string $resultPath): string
    {
        if (mb_strpos($resultPath, $this->dataDir) !== false) {
            $tmp = str_replace($this->dataDir, '', $resultPath);
        } else {
            throw new ZfeFiles_Exception('Невозможно определить относительный путь файла для ' . $resultPath);
        }

        return $tmp;
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Record_Exception
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public function absFilePath(): string
    {
        $file = $this->getRecord();
        return $this->dataDir . $file->get('path');
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public function getBaseDir(): string
    {
        return $this->dataDir;
    }

    public function getTempDir(): string
    {
        return $this->dataTempDir;
    }

    /**
     * Генерировать путь для файла.
     */
    protected function generatePath(
        string $basePath,
        int $id = 0,
        string $ext = '',
        bool $isUrl = false,
        bool $andRand = false
    ): string
    {
        $basePath = preg_replace('/\/+$/', '', $basePath);
        if (!is_writable($basePath)) {
            throw new ZfeFiles_Exception('Путь ' . $basePath . ' недоступен для записи');
        }

        if (mb_strlen($id) > $this->div) {
            $parts = str_split($id, $this->div);
        } else {
            $parts = [$id];
        }

        $fileName = array_pop($parts);
        $subPath = implode('/', $parts);

        if (!$isUrl && !file_exists($basePath . '/' . $subPath)) {
            $this->makePath($basePath . '/' . $subPath);
            $this->fixPath($basePath, $subPath);
        }

        return $basePath . '/' .
            (!empty($subPath) ? $subPath . '/' : '') .
            $fileName .
            (empty($ext) ? '' : '.' . $ext) .
            ($isUrl && $andRand ? '?r=' . mt_rand() : '');
    }

    /**
     * Безопасно рекурсивно создать директорию.
     * Если родительская директория отсутствует – создать.
     *
     * @throws Exception
     */
    protected function makePath(string $path): void
    {
        if (!file_exists($path)) {
            if (!@mkdir($path, 0755, true)) {
                throw new Exception("Mkdir failed for path '{$path}'");
            }
        }
    }

    /**
     * Настроить права на директорию файла.
     * Настраивать права на все папки от базовой до конкретной.
     *
     * @throws Zend_Exception
     */
    public function fixPath(string $basePath, string $subPath = null): void
    {
        $workPath = $basePath;
        $pathArr = explode('/', $subPath);
        foreach ($pathArr as $part) {
            $workPath .= '/' . $part;
            @chmod($workPath, 0777);
            @chown($workPath, $this->config->owner);
            @chgrp($workPath, $this->config->group);
        }
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Record_Exception
     * @throws Zend_Exception
     * @throws ZfeFiles_Exception
     */
    public function getResultPath(): string
    {
        $baseDir = $this->getBaseDir();
        if (empty($this->record->path)) {
            $path = $this->generatePath($baseDir, $this->record->id);
        } else {
            $path = $baseDir . $this->record->path;
        }

        // if (is_writable($path)) {
        //   $resultPath = $path . '/' . $this->getRecord()->title;
        //   return $resultPath;
        // }

        return $path;
    }

    /**
     * Копировать файлы в целевую директорию во время загрузки.
     */
    public function useCopy(): self
    {
        $this->method = 'copy';
        return $this;
    }

    /**
     * Перемещать файлы в целевую директорию во время загрузки.
     */
    public function useMove(): self
    {
        $this->method = 'rename';
        return $this;
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Record_Exception
     * @throws Zend_Exception
     */
    public function upload(string $fromPath = null): ZfeFiles_Model_File
    {
        $file = $this->getRecord();
        if (!$fromPath) {
            $mapper = new ZfeFiles_PathMapper($file);
            if ($mapper->isMapped()) {
                $fromPath = $mapper->getMapped();
            }
        }
        if (empty($fromPath)) {
            throw new ZfeFiles_Exception('Не указан путь, из которого надо переместить файл');
        }

        $resultPath = $this->getResultPath();

        if ($this->method == 'copy') {
            copy($fromPath, $resultPath);
        } else {
            rename($fromPath, $resultPath);
        }
        if (!file_exists($resultPath)) {
            throw new ZfeFiles_Exception(
                sprintf('Не удалось переместить файл из %s в %s', $fromPath, $resultPath)
            );
        }

        // set result path to record
        $relResultPath = $this->relateFilePath($resultPath);
        $file->set('path', $relResultPath);
        return $file;
    }

    /**
     * @throws ZfeFiles_Exception
     * @throws Doctrine_Record_Exception
     * @throws Zend_Exception
     */
    public function erase(): bool
    {
        try {
            $resultPath = $this->getResultPath();
            return @unlink($resultPath);
        } catch (ZfeFiles_Exception $e) {
            return true;
        }
    }
}
