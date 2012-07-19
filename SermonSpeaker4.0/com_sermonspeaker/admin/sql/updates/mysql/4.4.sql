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