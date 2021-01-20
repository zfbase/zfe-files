<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощник отправки файла средствами веб-сервера для встраивания в страницу.
 */
class ZfeFiles_Controller_Action_Helper_InlineDownload extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Отправить файл средствами сервера через авторизацию приложения.
     *
     * @param string $path путь до файла в файловой системе
     * @param string $url  защищенный виртуальный URL
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function direct(string $path, string $url): void
    {
        /** @var Zend_Config $config */
        $config = Zend_Registry::get('config');

        if (!$config->webserver) {
            throw new Zend_Controller_Action_Exception('В конфигурации не указан используемый веб-сервер (параметр webserver)');
        }

        $helpersMap = [
            'nginx' => 'DownloadNginx',
            'php' => 'DownloadPhp',
        ];
        if (array_key_exists($config->webserver, $helpersMap)) {
            /** @var ZfeFiles_Controller_Action_Helper_InlineDownload $action */
            $action = Zend_Controller_Action_HelperBroker::getStaticHelper($helpersMap[$config->webserver]);
            $action->direct($path, $url);
        } else {
            throw new Zend_Controller_Action_Exception('В конфигурации не указан не поддерживаемый веб-сервер', 500);
        }
    }

    /**
     * Сформировать заголовки ответа для отправки файла с принудительным скачиванием.
     *
     * @param string $path
     *
     * @return Zend_Controller_Response_Abstract
     */
    protected function factoryResponse($path)
    {
        $response = $this->getResponse();
        $response
            ->clearAllHeaders()
            ->clearBody()
        ;
        $response
            ->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Expires', '0')
            ->setHeader('Cache-Control', 'must-revalidate')
            ->setHeader('Pragma', 'public')
            ->setHeader('Content-Length', filesize($path))
        ;
        return $response;
    }
}
