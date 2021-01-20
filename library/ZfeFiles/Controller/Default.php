<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Стандартный контроллер для управления файлами.
 */
abstract class ZfeFiles_Controller_Default extends Controller_AbstractResource
{
    /**
     * {@inheritdoc}
     */
    protected static $_editFormName = ZfeFiles_Form_Default_File::class;

    /**
     * Загрузить файл.
     *
     * @throws Exception
     */
    public function uploadAction(): void
    {
        try {
            $agent = $this->getUploader()->upload($this->getAllParams());
            if ($agent === null) {
                $this->_json(static::STATUS_SUCCESS);
            } else {
                $this->_json(
                    static::STATUS_SUCCESS,
                    ['file' => $agent->getDataForUploader()],
                    "Файл {$agent->getFilename()} успешно загружен."
                );
            }
        } catch (Exception $ex) {
            ZFE_Utilities::popupException($ex);

            $this->_json(static::STATUS_FAIL, [], 'Не удалось загрузить файл: ' . $ex->getMessage());
        }
    }

    /**
     * Получить загрузчик файлов.
     */
    protected function getUploader(): ZfeFiles_Uploader_Interface
    {
        try {
            $uploaderName = Zend_Registry::get('config')->files->uploader ?? null;
        } catch (Zend_Exception $ex) {
            $uploaderName = null;
        }

        if (empty($uploaderName)) {
            $uploaderName = ZfeFiles_Uploader_DefaultAjax::class;
        }

        return new $uploaderName((static::$_modelName)::getManager());
    }

    /**
     * Скачать файл.
     *
     * @throws Zend_Exception
     */
    public function downloadAction(): void
    {
        $id = (int) $this->getParam('id');
        if (!$id) {
            $this->abort(400, 'Не указан обязательный параметр <code>id</code>');
        }

        $modelName = $this->getParam('model');
        $schemaCode = $this->getParam('schema');
        $relId = (int) $this->getParam('rel-id');

        /** @var ZfeFiles_Manager_Interface $manager */
        $manager = (static::$_modelName)::getManager();
        $agent = $relId
            ? $manager->getAgentByRelation($id, $modelName, $schemaCode, $relId)
            : $manager->getAgentByFileId($id);

        if (!$agent) {
            $this->abort(404, 'Файл не найден');
        }

        if (!$agent->isAllow('download')) {
            $this->abort(403, 'Вы не можете скачать этот файл.');
        }

        $this->_helper->download(
            $agent->getFile()->getRealPathHelper()->getPath(),
            $agent->getFile()->getWebPathHelper()->getVirtualPath(),
            $agent->getFilename()
        );
    }
}
