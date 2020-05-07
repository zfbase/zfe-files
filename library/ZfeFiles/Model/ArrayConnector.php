<?php

trait ZfeFiles_Model_ArrayConnector
{

    protected function _filesToArray(array $array): array
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $fileSchema) {
                $code = $fileSchema->getCode();
                $array[$code] = [];
                $files = ZfeFiles_Dispatcher::loadFiles($this, $code);
                foreach ($files as $file) {  /** @var ZfeFiles_FileInterface $file */
                    $array[$code][] = $file->getDataForUploader();
                }
            }
        }

        return $array;
    }

    protected function _filesFromArray(array $array): array
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $fileSchema) {
                $code = $fileSchema->getCode();
                if (array_key_exists($code, $array)) {
                    ZfeFiles_Dispatcher::updateFiles($this, $code, $array[$code]);
                    unset($array[$code]);
                }
            }
        }

        return $array;
    }
}
