<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощник отправки файла средствами php-сервера для встраивания в страницу.
 */
class ZfeFiles_Controller_Action_Helper_InlineDownloadPhp
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct(string $path): void
    {
        if (file_exists($path)) {
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: ' . (mime_content_type($path) ?: 'application/octet-stream'));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));

            // читаем файл и отправляем его пользователю
            readfile($path);
            exit;
        }

        throw new Zend_Controller_Action_Exception('Файл не найден', 404);
    }
}
