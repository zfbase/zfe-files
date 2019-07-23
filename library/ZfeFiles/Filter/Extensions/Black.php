<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Обезопасить расширение файла по черному списку.
 */
class ZfeFiles_Filter_Extensions_Black extends ZfeFiles_Filter_Extensions_Abstract
{
    protected $defaultList = [
        'php',
        'phtml',
        'sh',
        'exe',
        'bat',
    ];

    protected function check(string $ext, array $list = null): bool
    {
        return !in_array($ext, $list ?? $this->defaultList);
    }
}
