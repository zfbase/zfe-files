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
     * Название модели файла.
     */
    protected string $agentClassName;

    /**
     * Обработчик загрузки файла.
     */
    protected ZfeFiles_Uploader_Handler_Interface $uploadHandler;

    /**
     * Временная директория для загруженных файлов.
     */
    protected string $tempRoot;

    public function __construct(
        string $agentClassName = null,
        ZfeFiles_Uploader_Handler_Interface $uploadHandler = null,
        string $tempRoot = null
    )
    {
        $config = $this->fromConfig();

        $this->agentClassName = $agentClassName
            ?: $config->agentClassName
            ?? ZfeFiles_Agent_Mono::class;
        $this->uploadHandler = $uploadHandler
            ?: $config->uploadHandler
            ?? new ZfeFiles_Uploader_Handler_Default();
        $this->tempRoot = $tempRoot
            ?: $config->tempRoot
            ?? sys_get_temp_dir();
    }

    /**
     * @inheritDoc
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
        $itemId = $params['itemId'] ?? null;

        if ($chunksCount) {
            // Если грузим чанками, то через сессию контролируем загрузку.
            $session = new Zend_Session_Namespace('ChunksUploader-' . ($params['uid'] ?? null));
            if ($session->chunksCount === null) {
                $session->chunksCount = $chunksCount;
                $session->completeChunks = [];
                $session->chunkFilePrefix = uniqid();
            } elseif ($session->chunksCount !== $chunksCount) {
                throw new ZfeFiles_Uploader_Exception('Коллизия сессии загрузки файла чанками');
            }

            $chunkHash = $params['chunkHash'] ?? null;
            if ($chunkHash && $chunkHash != $uploadResult->getHash()) {
                throw new ZfeFiles_Uploader_Exception('Указанная хеш-сумма не совпадает с расчетной от загруженного чанка.');
            }

            $session->completeChunks[$chunkNum] = $uploadResult->getPath();

            if ($chunksCount === count($session->completeChunks)) {
                $fileSize = $params['fileSize'] ?? null;
                $fileName = $params['fileName'] ?? $uploadResult->getName();

                $tempPath = realpath($this->tempRoot) . DIRECTORY_SEPARATOR . time() . '-' . $fileName;
                $chunkPaths = $session->completeChunks;
                ksort($chunkPaths);  // выравнивание для многопоточной загрузки
                foreach ($chunkPaths as $chunkPath) {
                    $chunkSize = filesize($chunkPath);
                    $chunkFile = fopen($chunkPath, 'rb');
                    $chunkBlob = fread($chunkFile, $chunkSize);
                    fclose($chunkFile);

                    $tempFile = fopen($tempPath, 'ab');
                    fwrite($tempFile, $chunkBlob);
                    fclose($tempFile);

                    unlink($chunkPath);
                }
            } else {
                return null;
            }
        } else {
            $tempPath = $uploadResult->getPath();
            $fileName = $uploadResult->getName();
            $fileSize = $uploadResult->getSize();
        }

        $agent = ($this->agentClassName)::factory([
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
     *      @var ?string                              $agentClassName
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
                'agentClassName' => $this->config->files->agent ?? null,
                'uploadHandler' => $uhc ? new $uhc() : null,
                'tempRoot' => $this->config->files->tempPath ?? null,
            ];
        } catch(Zend_Exception $e) {
            return new stdClass();
        }
    }
}
