<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Определитель иконок в Bootstrap для файлов.
 */
class ZfeFiles_Icons_Bootstrap implements ZfeFiles_Icons_Interface
{
    /**
     * {@inheritdoc}
     */
    public function getFor(string $ext): string
    {
        return 'glyphicon glyphicon-file';
    }
}
