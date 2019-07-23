<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Интерфейс, для моделей, для которых возможна работа с файлами.
 */
interface ZfeFiles_Manageable
{
    /**
     * Получить файловый менеджер для объекта модели.
     *
     * @example return ZfeFiles_Manager::getDefault($this, $accessControl, $user);
     *
     * @param bool                           $accessControl управлять доступом к файлам для пользователя?
     * @param ZFE_Model_Default_Editors|null $user          обязателен, если $accessControl = true
     */
    public function getFileManager(bool $accessControl, ZFE_Model_Default_Editors $user = null): ZfeFiles_Manager_Abstract;
}
