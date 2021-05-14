<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный загрузчик по средством Ajax.
 */
class ZfeFiles_Uploader_DefaultAjax implements ZfeFiles_Uploader_Interface
{
    /**
     * Конфигурация.
     */
    protected Zend_Config $config;

    /**
     * Менеджер файла.
     */
    protected ZfeFiles_Manager_Interface $manager;

    /**
     * Обработчик загрузки файла.
     */
    protected ZfeFiles_Uploader_Handler_Interface $uploadHandler;

    /**
     * Временная директория для загруженных файлов.
     */
    protected string $tempRoot;

    public function __construct(
        ZfeFiles_Manager_Interface $manager,
        ZfeFiles_Uploader_Handler_Interface $uploadHandler = null,
        string $tempRoot = null
    ) {
        $config = $this->fromConfig();

        $this->manager = $manager;
        $this->uploadHandler = $uploadHandler
            ?: $config->uploadHandler
            ?? new ZfeFiles_Uploader_Handler_Default();
        $this->tempRoot = $tempRoot
            ?: $config->tempRoot
            ?? sys_get_temp_dir();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Zend_Session_Exception
     * @throws ZfeFiles_Uploader_Exception
     * @throws Exception
     */
    public function upload(array $params = []): ?ZfeFiles_Agent_Interface
    {
        $uploadResult = $this->uploadHandler->upload($params['field'] ?? 'file');

        $chunksCount = isset($params['chunksCount']) ? intval($params['chunksCount']) : null;
        $chunkNum = isset($params['chunkNum']) ? intval($params['chunkNum']) : null;
        $modelName = $params['modelName'] ?? null;
        $schemaCode = $params['schemaCode'] ?? null;
        $itemId = (!empty($params['itemId']) && $params['itemId'] !== 'null') ? ((int) $params['itemId']) : null;

        if ($chunksCount) {
            // Если грузим чанками, то через сессию контролируем загрузку.
            $session = new Zend_Session_Namespace('ChunksUploader-' . ($params['uid'] ?? null));
            if ($session->tempPath === null) {
                $session->tempPath = tempnam($this->tempRoot, 'zfe_');
            }

            $chunkHash = $params['chunkHash'] ?? null;
            if ($chunkHash && $chunkHash != $uploadResult->getHash()) {
                throw new ZfeFiles_Uploader_Exception('Указанная хеш-сумма не совпадает с расчетной от загруженного чанка.');
            }

            if ($session->lastInsertChunk === null) {
                if ($chunkNum != 0) {
                    throw new ZfeFiles_Uploader_Exception('Не корректный порядок чанков.');
                }

                $session->lastInsertChunk = 0;
            } elseif ($chunkNum == $session->lastInsertChunk + 1) {
                $session->lastInsertChunk++;
            } else {
                throw new ZfeFiles_Uploader_Exception('Не корректный порядок чанков.');
            }

            $chunkPath = $uploadResult->getPath();
            $chunkSize = filesize($chunkPath);
            $chunkFile = fopen($chunkPath, 'rb');
            $chunkBlob = fread($chunkFile, $chunkSize);
            fclose($chunkFile);

            $tempFile = fopen($session->tempPath, 'ab');
            fwrite($tempFile, $chunkBlob);
            fclose($tempFile);

            unlink($chunkPath);

            if ($chunkNum < $chunksCount - 1) {
                return null;
            }

            $tempPath = $session->tempPath;
            $fileSize = filesize($tempPath);
            $fileName = $params['fileName'] ?? $uploadResult->getName();
        } else {
            $tempPath = $uploadResult->getPath();
            $fileName = $uploadResult->getName();
            $fileSize = $uploadResult->getSize();
        }

        $agent = $this->manager->factory([
            'tempPath' => $tempPath,
            'fileName' => $fileName,
            'fileSize' => $fileSize,
            'modelName' => $modelName,
            'schemaCode' => $schemaCode,
            'itemId' => $itemId,
        ]);
        $agent->process();

        return $agent;
    }

    /**
     * Получить настройки из конфигурации.
     *
     * @return object {
     *      @var ?ZfeFiles_Uploader_Handler_Interface $uploadHandler
     *      @var ?string                              $tempRoot
     * }
     */
    protected function fromConfig(): stdClass
    {
        try {
            $this->config = Zend_Registry::get('config');
            $uhc = $this->config->files->uploadHandler ?? null;
            return (object) [
                'uploadHandler' => $uhc ? new $uhc() : null,
                'tempRoot' => $this->config->files->tempPath ?? null,
            ];
        } catch (Zend_Exception $e) {
            return new stdClass();
        }
    }
}
