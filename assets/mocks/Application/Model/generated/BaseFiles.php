<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Базовый класс файлов, автоматически генерируемый Doctrine ORM Framework.
 *
 * @property integer $id Идентификатор
 * @property integer $version
 * @property integer $creator_id
 * @property integer $editor_id
 * @property timestamp $datetime_created
 * @property timestamp $datetime_edited
 * @property integer $deleted
 * @property string $model_name Модель записи, к которой привязан файл
 * @property integer $item_id ID записи, к которой привязан файл
 * @property integer $type Тип связи с записью, к которой привязан файл (код в схеме)
 * @property string $title Название исходного файла
 * @property integer $size Размер файла в байтах
 * @property string $hash Хэш сумма от файла
 * @property string $ext Расширение файла
 * @property string $path Путь до файла
 */
class BaseFiles extends AbstractRecord
{
}
