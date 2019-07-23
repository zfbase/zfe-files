<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Обезопасить расширение файла.
 */
abstract class ZfeFiles_Filter_Extensions_Abstract implements ZfeFiles_Filter_Interface
{
    abstract protected function check(string $ext): bool;

    public function filter(string $fileName, array $list = null): string
    {
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $safe = $this->check($ext, $list) ? '' : '_';
        return $name . '.' . $safe . $ext;
    }
}
