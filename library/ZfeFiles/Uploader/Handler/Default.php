<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

class ZfeFiles_Uploader_Handler_Default implements ZfeFiles_Uploader_Handler_Interface
{
    /**
     * {@inheritdoc}
     *
     * @throws ZfeFiles_Uploader_Exception
     * @throws Zend_Exception
     */
    public function upload(string $field = 'file'): ZfeFiles_Uploader_Result
    {
        if (empty($_FILES[$field])) {
            throw new ZfeFiles_Uploader_Exception('Файл не указан.');
        }

        switch ($_FILES[$field]['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new ZfeFiles_Uploader_Exception('Файл слишком большой.', $_FILES[$field]['error']);
            case UPLOAD_ERR_PARTIAL:
                throw new ZfeFiles_Uploader_Exception('Загрузка прервана.', $_FILES[$field]['error']);
            case UPLOAD_ERR_NO_FILE:
                throw new ZfeFiles_Uploader_Exception('Файл не загружен.', $_FILES[$field]['error']);
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                throw new ZfeFiles_Uploader_Exception('Не удалось сохранить файл.', $_FILES[$field]['error']);
        }

        $hash = hash_file(config('files.hashAlgo', 'md5'), $_FILES[$field]['tmp_name']);
        $path = realpath(config('files.tempPath', sys_get_temp_dir())) . DIRECTORY_SEPARATOR . time() . $hash;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
            throw new ZfeFiles_Uploader_Exception('Не удалось переместить файл из директории загрузки.');
        }

        $result = new ZfeFiles_Uploader_Result();
        $result
            ->setName($_FILES[$field]['name'])
            ->setSize($_FILES[$field]['size'])
            ->setPath($path)
            ->setHash($hash)
        ;
        return $result;
    }
}
