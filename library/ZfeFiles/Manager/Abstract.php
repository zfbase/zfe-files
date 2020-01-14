<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Абстрактный менеджер файлов.
 */
abstract class ZfeFiles_Manager_Abstract
{
    /**
     * Получить соответствующий файлу менеджер.
     */
    public static function loadForFile(ZfeFiles_FileInterface $file): self
    {
    }
}
