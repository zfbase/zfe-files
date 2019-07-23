<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Проверки прав на основные операции с файлами.
 */
class ZfeFiles_Accessor_Acl extends ZfeFiles_Accessor
{
    /**
     * Проверить права на просмотр всех файлов записи списком.
     */
    public function isAllowToView(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'view');
    }

    /**
     * Проверить права на управление файлами.
     */
    public function isAllowToControl(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'control');
    }

    /**
     * Проверить права на удаление файла.
     */
    public function isAllowToDelete(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'delete');
    }

    /**
     * Проверить права на скачивание файла.
     */
    public function isAllowToDownload(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'download');
    }

    /**
     * Проверить права на скачивание файлов одним архивом.
     */
    public function isAllowToDownloadAll(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'download-all');
    }

    /**
     * Проверить права на обработку файлов.
     */
    public function isAllowToProcess(): bool
    {
        return $this->acl->isAllowed($this->role, $this->controller, 'process');
    }
}
