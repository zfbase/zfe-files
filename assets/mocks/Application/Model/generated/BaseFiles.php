<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Базовый класс пользователей, автоматически генерируемый Doctrine ORM Framework.
 *
 * @property integer $id
 * @property string $model_name
 * @property integer $item_id
 * @property string $schema_code
 * @property string $title
 * @property integer $size
 * @property string $hash
 * @property string $ext
 * @property string $path
 * @property integer $version
 * @property integer $creator_id
 * @property integer $editor_id
 * @property timestamp $datetime_created
 * @property timestamp $datetime_edited
 * @property integer $deleted
 */
class BaseFiles extends AbstractRecord
{
}
