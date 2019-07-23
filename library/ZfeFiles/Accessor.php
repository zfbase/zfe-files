<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Стандартный контролер управления файлами.
 */
abstract class ZfeFiles_Accessor extends ZfeFiles_ManageableAccess
{
    /**
     * @var Zend_Acl
     */
    protected $acl;

    /**
     * @var ZFE_Model_Default_Editors
     */
    protected $user;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var string
     */
    protected $controller = 'files';

    public function __construct(Zend_Acl $acl, ZFE_Model_Default_Editors $user, string $role = null)
    {
        $this->acl = $acl;
        $this->user = $user;
        $this->role = $role ?? $user->role;
    }

    public function getUser(): ZFE_Model_Default_Editors
    {
        return $this->user;
    }

    public function setUser(ZFE_Model_Default_Editors $user): self
    {
        $this->user = $user;
        return $this;
    }

    protected function generateURL(string $action, ZfeFiles_Model_File $file = null): string
    {
        $r = $this->getRecord();
        $rClass = get_class($r);
        $url = sprintf(
            '/%s/%s/m/%s/id/%d',
            $this->controller,
            $action,
            $rClass,
            $r->id
        );
        if ($file) {
            $url .= '/fid/' . $file->id;
        }
        return $url;
    }

    /**
     * Проверить прав на просмотр всех файлов записи списком.
     */
    public function isAllowToView(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на просмотр файлов записи списком.
     */
    public function getViewURL(): ?string
    {
        if ($this->isAllowToView()) {
            return $this->generateURL('view');
        }
        return null;
    }

    /**
     * Проверить прав на просмотр всех файлов записи списком.
     */
    public function isAllowToControl(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на просмотр файлов записи списком.
     */
    public function getControlURL(): ?string
    {
        if ($this->isAllowToControl()) {
            return $this->generateURL('control');
        }
        return null;
    }

    /**
     * Проверить права на удаление файла.
     */
    public function isAllowToDelete(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на удаление файла записи.
     */
    public function getDeleteURL(ZfeFiles_Model_File $file): ?string
    {
        if ($this->isAllowToDelete()) {
            return $this->generateURL('delete', $file);
        }
        return null;
    }

    /**
     * Проверить права на скачивание файла.
     */
    public function isAllowToDownload(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на скачивание файла записи.
     */
    public function getDownloadURL(ZfeFiles_Model_File $file): ?string
    {
        if ($this->isAllowToDownload()) {
            return $this->generateURL('download', $file);
        }
        return null;
    }

    /**
     * Проверить права на скачивание файлов одним архивом.
     */
    public function isAllowToDownloadAll(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на скачивание скачивание файлов записи одним архивом.
     */
    public function getDownloadAllURL(): ?string
    {
        if ($this->isAllowToDownloadAll()) {
            return $this->generateURL('download-all');
        }
        return null;
    }

    /**
     * Проверить права на скачивание файлов одним архивом.
     */
    public function isAllowToProcess(): bool
    {
        return true;
    }

    /**
     * Получить ссылку на скачивание скачивание файлов записи одним архивом.
     */
    public function getProcessURL(ZfeFiles_Model_File $file): ?string
    {
        if ($this->isAllowToDownload()) {
            return $this->generateURL('process', $file);
        }
        return null;
    }

    final public function decomposeURL(string $url): array
    {
        $parts = explode('/', $url);
        $action = $parts[2];
        $params = ['m' => $parts[4], 'id' => $parts[6]];
        if (count($parts) > 7) {
            $params['fid'] = $parts['8'];
        }
        return [
            'module' => null,
            'controller' => $this->controller,
            'action' => $action,
            'params' => $params,
        ];
    }
}
