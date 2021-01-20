<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощник отправки файла средствами php-сервера для встраивания в страницу.
 */
class ZfeFiles_Controller_Action_Helper_InlineDownloadPhp extends ZfeFiles_Controller_Action_Helper_InlineDownload
{
    /**
     * {@inheritdoc}
     */
    public function direct(string $path, string $url): void
    {
        if (file_exists($path)) {
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }

            $response = $this->factoryResponse($path);
            $response->sendResponse();

            // читаем файл и отправляем его пользователю
            readfile($path);
            exit;
        }

        throw new Zend_Controller_Action_Exception('Файл не найден', 404);
    }
}
