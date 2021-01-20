<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощник отправки файла средствами веб-сервера nginx через авторизацию приложения.
 */
class ZfeFiles_Controller_Action_Helper_InlineDownloadNginx extends ZfeFiles_Controller_Action_Helper_InlineDownload
{
    /**
     * {@inheritdoc}
     */
    public function direct(string $path, string $url): void
    {
        if (file_exists($path)) {
            $response = $this->factoryResponse($path);
            $response->setHeader('X-Accel-Redirect', $url);
            $response->sendResponse();
            exit;
        }

        throw new Zend_Controller_Action_Exception('Файл не найден', 404);
    }
}
