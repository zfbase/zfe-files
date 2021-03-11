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
     * @var Число разрядов для разбиения идентификатора
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
                throw new ZfeFiles_Exception("Не удалось создать директорию ${path}");
            }
        }

        if (!is_dir($path)) {
            throw new ZfeFiles_Exception("${path} не является директорией");
        }

        if ($mode !== octdec(mb_substr(decoct(fileperms($path)), -4)) && !chmod($path, $mode)) {
            throw new ZfeFiles_Exception("Не удалось изменить права доступа к директории ${path}");
        }
    }

    /**
     * Получить расширение файла по имени файла.
     */
    public static function extensionFromFilename(string $name): ?string
    {
        $pathParts = explode('/', $name);
        $filename = end($pathParts);

        if (mb_strpos($filename, '.') === false) {
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
        $startPosition = (int) mb_strrpos($fileName, DIRECTORY_SEPARATOR);
        $dotPosition = mb_strrpos($fileName, '.', $startPosition);
        if ($dotPosition === false) {
            return $fileName;
        }

        return mb_substr($fileName, 0, $dotPosition);
    }

    /**
     * Получить расширения, соответствующие MIME-типу.
     *
     * @return string[]|null
     */
    public static function getExtensionByMimeType(string $mime)
    {
        $map = include_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'assets', 'mime-types-map', 'mime2ext.php']);
        return $map[$mime] ?? null;
    }

    /**
     * Получить MIME-типы, соответствующие расширения.
     *
     * @return string[]|null
     */
    public static function getMimeTypeByExtension(string $ext)
    {
        $map = include_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'assets', 'mime-types-map', 'ext2mime.php']);
        return $map[$ext] ?? null;
    }

    /**
     * Получить абсолютный адрес корня ZFE Files.
     *
     * Необходим для автозагрузчика ZF.
     */
    public static function getRoot(): string
    {
        return __DIR__;
    }
}
