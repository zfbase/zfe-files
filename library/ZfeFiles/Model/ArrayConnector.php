<?php

trait ZfeFiles_Model_ArrayConnector
{
    protected function _filesToArray(array $array): array
    {
        if ($this instanceof ZfeFiles_Manageable) {
            foreach (static::getFileSchemas() as $fileSchema) {
                $code = $fileSchema->getCode();
                $array[$code] = [];
                $manager = ($fileSchema->getModel())::getManager();
                $agents = $manager->getAgentsBySchema($this, $code);
                foreach ($agents as $agent) {  /** @var ZfeFiles_Agent_Interface $agent */
                    $array[$code][] = $agent->getDataForUploader();
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
                    $manager = ($fileSchema->getModel())::getManager();
                    $manager->updateForSchema($this, $code, $array[$code]);
                    unset($array[$code]);
                }
            }
        }

        return $array;
    }
}
