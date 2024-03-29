<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Трейт для внедрения в AbstractRecord, добавляющий функционал ZfeFiles.
 */
trait ZfeFiles_Model_Injection
{
    /**
     * Агенты файлов.
     *
     * @var array<ZfeFiles_Agent_Interface[]>
     */
    private $_agents = [];

    /**
     * Получить агенты файлов для схемы.
     *
     * @return ZfeFiles_Agent_Interface[]
     */
    public function getAgents(ZfeFiles_Schema_Default $schema, bool $load = true): array
    {
        $code = $schema->getCode();
        if (!isset($this->_agents[$code])) {
            if ($load) {
                $this->_agents[$code] = $this->getAgentsFromDb($schema);
            } else {
                $this->_agents[$code] = [];
            }
        }
        return $this->_agents[$code];
    }

    /**
     * Получить агенты файлов для схемы.
     *
     * @return ZfeFiles_Agent_Interface[]
     */
    public function getAgentsByCode(string $code, bool $load = true): array
    {
        if (!isset($this->_agents[$code])) {
            if ($load) {
                $schema = static::getFileSchemas()->getByCode($code);
                $this->_agents[$code] = $this->getAgentsFromDb($schema);
            } else {
                $this->_agents[$code] = [];
            }
        }
        return $this->_agents[$code];
    }

    /**
     * Получить агенты файлов для схемы по данным из базы.
     *
     * @return ZfeFiles_Agent_Interface[]
     */
    public function getAgentsFromDb(ZfeFiles_Schema_Default $schema): array
    {
        return ($schema->getModel())::getManager()
            ->getAgentsBySchema($this, $schema->getCode())
        ;
    }

    /**
     * Обновить агенты из базы.
     */
    public function refreshAgents(): void
    {
        /** @var ZfeFiles_Schema_Default $schema */
        foreach (static::getFileSchemas() as $schema) {
            $this->_agents[$schema->getCode()] = $this->getAgentsFromDb($schema);
        }
    }

    /**
     * Установить список агентов.
     *
     * @param ZfeFiles_Agent_Interface[] $agents
     */
    public function setAgents(string $code, array $agents): void
    {
        $this->_agents[$code] = $agents;
    }

    /**
     * Добавить агента для записи.
     *
     * @param string $code
     * @param ZfeFiles_Agent_Interface $agent
     *
     * @return ZfeFiles_Manageable
     */
    public function addAgent(string $code, ZfeFiles_Agent_Interface $agent): ZfeFiles_Manageable
    {
        $this->_agents[$code][] = $agent;
        return $this;
    }

    /**
     * Сохранить данные агентов в БД.
     */
    public function updateAgentsInDb(string $code): void
    {
        /** @var ZfeFiles_Schema_Default $schema */
        $schema = static::getFileSchemas()->getByCode($code);

        /** @var ZfeFiles_Manager_Interface $manager */
        $manager = ($schema->getModel())::getManager();
        $manager->updateAgents($this, $schema, $this->getAgents($schema));
    }

    //
    // Расширения базовых функций Doctrine для поддержки ZFE Files.
    //

    /**
     * Расширение toArray для привязанных файлов.
     */
    protected function _filesToArray(array $array): array
    {
        if ($this instanceof ZfeFiles_Manageable) {
            /** @var ZfeFiles_Schema_Default $schema */
            foreach (static::getFileSchemas() as $schema) {
                $code = $schema->getCode();
                $agents = $this->getAgents($schema);
                foreach ($agents as $agent) {
                    $file = $agent->getFile();
                    if ($file && $file->exists() && !$file->isDeleted()) {
                        $array[$code][] = $agent->getDataForUploader();
                    }
                }
            }
        }
        return $array;
    }

    /**
     * Расширение fromArray для привязанных файлов.
     */
    protected function _filesFromArray(array $array): array
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $schema) {
                $code = $schema->getCode();
                if (array_key_exists($code, $array) && $array[$code] !== null) {
                    /** @var ZfeFiles_Manager_Interface $manager */
                    $manager = ($schema->getModel())::getManager();
                    $agents = $manager->createAgents($array[$code], $code, $this);
                    $this->setAgents($code, $agents);
                    unset($array[$code]);
                }
            }
        }
        return $array;
    }

    /**
     * Расширение postInsert для привязки файлов к новым записям.
     */
    protected function _filesPostInsert(): void
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $schema) {
                $code = $schema->getCode();
                foreach ($this->getAgents($schema) as $agent) {
                    $agent->linkManageableItem($code, $this);
                }
            }
        }
    }

    /**
     * Расширение postSave для привязанных файлов.
     */
    protected function _filesPostSave(): void
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $schema) {
                /** @var ZfeFiles_Manager_Interface $manager */
                $manager = ($schema->getModel())::getManager();
                $manager->updateAgents($this, $schema, $this->getAgents($schema));
                $manager->process(static::class, $this->id, $schema->getCode());
            }
        }
    }
}
