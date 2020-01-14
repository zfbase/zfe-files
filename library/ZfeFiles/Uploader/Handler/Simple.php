<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Uploader_Handler_Simple implements ZfeFiles_Uploader_Handler_Interface
{
    /**
     * {@inheritdoc}
     */
    public function upload(): ZfeFiles_Uploader_Result
    {
        if (empty($_FILES['file'])) {
            throw new ZfeFiles_Uploader_Exception('Файл не указан.');
        }

        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ZfeFiles_Uploader_Exception('Файл слишком большой.');
            case UPLOAD_ERR_PARTIAL:
                throw new ZfeFiles_Uploader_Exception('Загрузка прервана.');
            case UPLOAD_ERR_NO_FILE:
                throw new ZfeFiles_Uploader_DefaultAjax('Файл не загружен.');
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                throw new ZfeFiles_Uploader_Exception('Не удалось сохранить файл.');
        }

        $result = new ZfeFiles_Uploader_Result();
        $result
            ->setName($_FILES['file']['name'])
            ->setPath($_FILES['file']['tmp_name'])
            ->setSize($_FILES['file']['size'])
        ;
        return $result;
    }
}
