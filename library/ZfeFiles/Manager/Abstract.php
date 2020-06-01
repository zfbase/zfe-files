<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Базовый абстрактный менеджер файлов.
 */
abstract class ZfeFiles_Manager_Abstract implements ZfeFiles_Manager_Interface
{
    /**
     * Модель файлов.
     */
    protected string $fileModelName = 'Files';

    /**
     * Контроллер файлов.
     */
    protected string $controllerName;

    /**
     * Класс агентов.
     */
    protected string $agentClassName;

    /**
     * Обработчик.
     *
     * @var ZfeFiles_Handler_Interface|string|null
     */
    protected $handler;

    /**
     * Конструктор.
     *
     * @param Zend_Config|array|string $config конфигурация или её имя в общей конфигурации
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    public function __construct($config = null)
    {
        if ($config instanceof Zend_Config) {
            $this->setOptions($config->toArray());
        } elseif (is_array($config)) {
            $this->setOptions($config);
        } elseif (is_string($config)) {
            $this->setOptions(Zend_Registry::get('config')->{$config}->toArray());
        } elseif ($config === null) {
            $settings = Zend_Registry::get('config')->get('files');
            if ($settings) {
                $this->setOptions($settings->toArray());
            }
        } else {
            throw new ZfeFiles_Exception(
                '$config должен быть массивом, экземпляром Zend_Config или именем ветви в application.ini'
            );
        }
    }

    /**
     * Установить опциональные параметры менеджера.
     *
     * @throws ZfeFiles_Exception
     */
    protected function setOptions(array $options): void
    {
        if (!empty($options['fileModelName'])) {
            if (!is_string($options['fileModelName'])) {
                throw new ZfeFiles_Exception('"fileModelName" должен быть строкой с именем класса');
            } elseif (is_a($options['fileModelName'], ZfeFiles_File_OriginInterface::class, true)) {
                $this->fileModelName = $options['fileModelName'];
            } else {
                throw new ZfeFiles_Exception('Модель файла должна реализовывать интерфейс ZfeFiles_File_OriginInterface');
            }
        }

        $this->controllerName = $options['controllerName']
            ?? ($this->fileModelName)::getControllerName();

        if (!empty($options['agentClassName'])) {
            if (!is_string($options['agentClassName'])) {
                throw new ZfeFiles_Exception('');
            } elseif (is_a($options['agentClassName'], ZfeFiles_Agent_Interface::class, true)) {
                $this->agentClassName = $options['agentClassName'];
            } else {
                throw new ZfeFiles_Exception('Класс агента должен реализовывать интерфейс ZfeFiles_Agent_Interface');
            }
        }

        if (!empty($options['handler'])) {
            if (is_a($options['handler'], ZfeFiles_Handler_Interface::class, true)) {
                $this->handler = $options['handler'];
            } else {
                throw new ZfeFiles_Exception('Обработчик должен реализовывать интерфейс ZfeFiles_Handler_Interface');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getUploadUrl(): string
    {
        return "/{$this->controllerName}/upload";
    }

    /**
     * Получить хеш-сумму для файла.
     *
     * @throws ZfeFiles_Exception
     * @throws Zend_Exception
     */
    protected function hash(string $path): string
    {
        if (!file_exists($path) || !is_file($path)) {
            throw new ZfeFiles_Exception('Невозможно рассчитать хеш-сумму от файла – Файл не найден');
        }

        if (!is_readable($path)) {
            throw new ZfeFiles_Exception('Невозможно рассчитать хеш-сумму от файла – Файл не доступен для чтения');
        }

        if (is_a($this->fileModelName, ZfeFiles_File_CustomHashable::class, true)) {
            return ($this->fileModelName)::hash($path);
        }

        return hash_file(Zend_Registry::get('config')->files->hashAlgo ?? 'crc32', $path);
    }

    /**
     * @inheritDoc
     */
    public function getHandler(): ?ZfeFiles_Handler_Interface
    {
        return is_string($this->handler)
            ? new $this->handler
            : $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function getAgentByFileId(int $id): ?ZfeFiles_Agent_Interface
    {
        /** @var ZfeFiles_File_OriginInterface $file */
        $file = ($this->fileModelName)::find($id);
        return $file ? $this->getAgentByFile($file) : null;
    }

    public function getAgentByFile(ZfeFiles_File_OriginInterface $file): ZfeFiles_Agent_Interface
    {
        return new $this->agentClassName($file);
    }

    /**
     * Переместить файл из временного расположения в постоянное.
     *
     * @param ZfeFiles_File_Interface|Files $file
     * @throws ZfeFiles_Exception
     */
    protected function move(ZfeFiles_File_Interface $file, string $tempPath = null): void
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
     * Извлечь ID из массива чисел и JSON.
     */
    protected function extractIds(array $rows): array
    {
        $ids = [];
        foreach ($rows as $row) {
            if (is_numeric($row)) {
                $ids[] = (int) $row;
            } else {
                $ids[] = (int) json_decode($row)->id;
            }
        }
        return $ids;
    }

    /**
     * Извлечь дополнительные данные из массива чисел и JSON.
     */
    protected function extractData(array $rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            if (is_numeric($row)) {
                $data[(int) $row] = [];
            } else {
                $json = (array) json_decode($row);
                $id = (int) $json['id'];
                unset($json['id']);
                $data[$id] = $json;
            }
        }
        return $data;
    }
}