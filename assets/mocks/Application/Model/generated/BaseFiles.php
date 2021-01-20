<?php

/*
 * Единая точка загрузки и управления файлами для приложений на ZFE.
 */

/**
 * Базовый класс пользователей, автоматически генерируемый Doctrine ORM Framework.
 *
 * @property int       $id
 * @property string    $model_name
 * @property int       $item_id
 * @property string    $schema_code
 * @property string    $title
 * @property int       $size
 * @property string    $hash
 * @property string    $extension
 * @property string    $path
 * @property int       $version
 * @property int       $creator_id
 * @property int       $editor_id
 * @property timestamp $datetime_created
 * @property timestamp $datetime_edited
 * @property int       $deleted
 */
class BaseFiles extends AbstractRecord
{
}
