<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощники для работы с файлами.
 */
class ZfeFiles_Helpers
{
    /**
     * @var Число разрядов для разбиения идентификатора.
     */
    const DIVIDE = 3;

    /**
     * Проверить и подготовить директорию.
     *
     * @throws ZfeFiles_Exception
     */
    public static function prepareDirectory(string $path, int $mode = 0777): void
    {
        if (!file_exists($path)) {
            if (!mkdir($path, $mode, true)) {
                throw new ZfeFiles_Exception("Не удалось создать директорию $path");
            }
        }

        if (!is_dir($path)) {
            throw new ZfeFiles_Exception("$path не является директорией");
        }

        if ($mode !== octdec(substr(decoct(fileperms($path)), -4)) && !chmod($path, $mode)) {
            throw new ZfeFiles_Exception("Не удалось изменить права доступа к директории $path");
        }
    }

    /**
     * Получить расширение файла по имени файла.
     */
    public static function extensionFromFilename(string $name): ?string
    {
        $pathParts = explode('/', $name);
        $filename = end($pathParts);

        if (strpos($filename, '.') === false) {
            return null;
        }

        $nameParts = explode('.', $filename);
        return mb_strtolower(end($nameParts));
    }

    /**
     * Отрезать от имени файла расширение.
     */
    public static function cutExtension(string $fileName): ?string
    {
        $startPosition = (int) strrpos($fileName, DIRECTORY_SEPARATOR);
        $dotPosition = strrpos($fileName, '.', $startPosition);
        if ($dotPosition === false) {
            return $fileName;
        }

        return substr($fileName, 0, $dotPosition);
    }

    /**
     * Получить абсолютный адрес корня ZFE Files.
     *
     * Необходим для автозарузчика ZF.
     */
    public static function getRoot(): string
    {
        return __DIR__;
    }
}
