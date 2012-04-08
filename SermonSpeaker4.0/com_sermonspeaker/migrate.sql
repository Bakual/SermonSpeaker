ALTER TABLE #__sermon_speakers ADD COLUMN `alias` VARCHAR(255) NOT NULL;
ALTER TABLE #__sermon_series ADD COLUMN `alias` VARCHAR(255) NOT NULL;
ALTER TABLE #__sermon_speakers ADD COLUMN `metakey` TEXT NOT NULL, ADD `metadesc` TEXT NOT NULL;
ALTER TABLE #__sermon_series ADD COLUMN `metakey` TEXT NOT NULL, ADD `metadesc` TEXT NOT NULL;
ALTER TABLE #__sermon_sermons CHANGE `created_on` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_series CHANGE `created_on` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_speakers CHANGE `created_on` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_sermons CHANGE `published` `state` TINYINT(3) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_speakers CHANGE `published` `state` TINYINT(3) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_series CHANGE `published` `state` TINYINT(3) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_sermons CHANGE `sermon_number` `sermon_number` INT(10) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_sermons CHANGE `sermon_path` `audiofile` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons ADD `videofile` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons ADD `picture` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons CHANGE `sermon_date` `sermon_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_series ADD `home` TINYINT(3) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_speakers ADD `home` TINYINT(3) NOT NULL DEFAULT '0';
CREATE TABLE IF NOT EXISTS `#__sermon_scriptures` (
	`book` INT(3) NOT NULL DEFAULT '0',
	`cap1` INT(3) NOT NULL DEFAULT '0',
	`vers1` INT(4) NOT NULL DEFAULT '0',
	`cap2` INT(3) NOT NULL DEFAULT '0',
	`vers2` INT(4) NOT NULL DEFAULT '0',
	`text` MEDIUMTEXT NOT NULL DEFAULT '',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`sermon_id` INT(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
INSERT INTO `#__sermon_scriptures`
	(`text`,`sermon_id`) 
	SELECT `sermon_scripture`, `id` FROM `#__sermon_sermons` WHERE `sermon_scripture` != '';
ALTER TABLE `#__sermon_sermons` DROP `sermon_scripture`;
ALTER TABLE #__sermon_sermons ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_sermons ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_sermons ADD `language` CHAR(7) NOT NULL;
ALTER TABLE #__sermon_speakers ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_speakers ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_speakers ADD `language` CHAR(7) NOT NULL;
ALTER TABLE #__sermon_series ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_series ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_series ADD `language` CHAR(7) NOT NULL;
