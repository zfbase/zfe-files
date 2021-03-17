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
    public static function extensionFromFilename(string $path): ?string
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = end($pathParts);

        if (mb_strpos($fileName, '.') === false) {
            return null;
        }

        $nameParts = explode('.', $fileName);
        $ext = mb_strtolower(array_pop($nameParts));

        return ($ext == 'crdownload')
            ? mb_strtolower(array_pop($nameParts))
            : $ext;
    }

    /**
     * Отрезать от имени файла расширение.
     */
    public static function cutExtension(string $path): ?string
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = end($pathParts);

        if (mb_strpos($fileName, '.') === false) {
            return $fileName;
        }

        $nameParts = explode('.', $fileName);
        $ext = mb_strtolower(array_pop($nameParts));
        if ($ext == 'crdownload') {
            array_pop($nameParts);
        }

        return implode('.', $nameParts);
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
