<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Помощник отправки файла средствами веб-сервера для встраивания в страницу.
 */
class ZfeFiles_Controller_Action_Helper_InlineDownload
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct(string $path, string $url): void
    {
        /** @var Zend_Config $config */
        $config = Zend_Registry::get('config');

        if (!$config->webserver) {
            throw new Zend_Controller_Action_Exception('В конфигурации не указан используемый веб-сервер (параметр webserver)');
        }

        switch ($config->webserver) {
            case 'nginx':
                Zend_Controller_Action_HelperBroker::getStaticHelper('InlineDownloadNginx')->direct($path, $url);
                break;
            case 'php':
                Zend_Controller_Action_HelperBroker::getStaticHelper('InlineDownloadPhp')->direct($path);
                break;
            default:
                throw new Zend_Controller_Action_Exception('В конфигурации не указан не поддерживаемый веб-сервер', 500);
        }
    }
}
