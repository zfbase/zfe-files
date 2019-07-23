<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Обезопасить расширение файла по белому списку.
 */
class ZfeFiles_Filter_Extensions_White extends ZfeFiles_Filter_Extensions_Abstract
{
    protected function check(string $ext, array $list = null): bool
    {
        if (!$list) {
            throw new DomainException('Необходимо указать список допустимых расширений.');
        }
        return in_array($ext, $list);
    }
}
