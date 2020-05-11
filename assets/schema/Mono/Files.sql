CREATE TABLE IF NOT EXISTS `orpheus`.`files` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
  `version` INT(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Версия',
  `creator_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Создал',
  `editor_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Последним изменил',
  `datetime_created` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата и время создания',
  `datetime_edited` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата и время последнего изменения',
  `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Удалено',
  `model_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Модель записи, к которой привязан файл',
  `item_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'ID записи, к которой привязан файл',
  `schema_code` VARCHAR(31) NULL DEFAULT NULL COMMENT 'Код связи с записью, к которой привязан файл (код в схеме)',
  `title` VARCHAR(511) NOT NULL COMMENT 'Название исходного файла',
  `size` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Размер файла в байтах',
  `hash` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Хэш сумма от файла',
  `extension` VARCHAR(7) NULL DEFAULT NULL COMMENT 'Расширение файла',
  `path` VARCHAR(511) NOT NULL COMMENT 'Путь до файла',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;
