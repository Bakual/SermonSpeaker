DROP TABLE IF EXISTS `#__sermon_speakers`;
DROP TABLE IF EXISTS `#__sermon_series`;
DROP TABLE IF EXISTS `#__sermon_sermons`;
DROP TABLE IF EXISTS `#__sermon_scriptures`;
 
CREATE TABLE `#__sermon_speakers` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`website` TEXT,
	`intro` MEDIUMTEXT,
	`bio` MEDIUMTEXT,
	`pic` TEXT,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`home` TINYINT(3) NOT NULL DEFAULT '0',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` INT(10) NOT NULL DEFAULT '0',
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	`metadata` TEXT NOT NULL,
	`checked_out` INT(11) NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`language` CHAR(7) NOT NULL DEFAULT '*',
	`version` int(10) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__sermon_series` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`series_description` MEDIUMTEXT NOT NULL,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`home` TINYINT(3) NOT NULL DEFAULT '0',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` INT(10) NOT NULL DEFAULT '0',
	`avatar` TEXT,
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	`metadata` TEXT NOT NULL,
	`checked_out` INT(11) NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`zip_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`zip_content` TEXT NOT NULL,
	`zip_progress` INT(11) NOT NULL DEFAULT '0',
	`zip_state` TINYINT(3) NOT NULL DEFAULT '0',
	`zip_size` INT(11) NOT NULL DEFAULT '0',
	`zip_dl` TINYINT(3) NOT NULL DEFAULT '0',
	`language` CHAR(7) NOT NULL DEFAULT '*',
	`version` int(10) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__sermon_sermons` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`speaker_id` INT(10) NOT NULL DEFAULT '0',
	`series_id` INT(10) NOT NULL DEFAULT '0',
	`audiofile` TEXT NOT NULL DEFAULT '',
	`videofile` TEXT NOT NULL DEFAULT '',
	`audiofilesize` INT NOT NULL DEFAULT '0',
	`videofilesize` INT NOT NULL DEFAULT '0',
	`picture` TEXT NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`sermon_number` INT(10) NOT NULL DEFAULT '0',
	`custom1` MEDIUMTEXT NOT NULL,
	`custom2` MEDIUMTEXT NOT NULL,
	`sermon_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`sermon_time` TIME NOT NULL DEFAULT '00:00:00',
	`notes` LONGTEXT NOT NULL,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` INT(10) NOT NULL DEFAULT '0',
	`podcast` TINYINT(1) NOT NULL DEFAULT '0',
	`addfile` TEXT NOT NULL,
	`addfileDesc` VARCHAR(255) NOT NULL,
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	`metadata` TEXT NOT NULL,
	`checked_out` INT(11) NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`language` CHAR(7) NOT NULL DEFAULT '*',
	`version` int(10) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__sermon_scriptures` (
	`book` INT(3) NOT NULL DEFAULT '0',
	`cap1` INT(3) NOT NULL DEFAULT '0',
	`vers1` INT(4) NOT NULL DEFAULT '0',
	`cap2` INT(3) NOT NULL DEFAULT '0',
	`vers2` INT(4) NOT NULL DEFAULT '0',
	`text` MEDIUMTEXT NOT NULL DEFAULT '',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`sermon_id` INT(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__sermon_speakers`
	(`id`,`title`,`alias`,`website`,`intro`,`bio`,`pic`,`state`,`ordering`,`created_by`,`created`,`home`) 
	VALUES (1,'Billy Sunday','billy-sunday','http://www.sermonspeaker.net','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.',"This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God's Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.",'components/com_sermonspeaker/media/default_speaker.jpg',1,1,0,'2006-03-28','1');

INSERT INTO `#__sermon_series`
	(`id`,`title`,`alias`,`series_description`,`state`,`ordering`,`created_by`,`created`,`home`) 
	VALUES (1,'General Topics','general-topics','Topics of general interest.',1,1,0,'2006-03-28','1');

INSERT INTO `#__sermon_sermons`
	(`id`,`speaker_id`,`series_id`,`audiofile`,`title`,`alias`,`sermon_number`,`sermon_date`,`sermon_time`,`notes`,`ordering`,`created_by`,`created`,`state`) 
	VALUES (1,1,1,'/components/com_sermonspeaker/media/default_sermon.mp3','The Sin of Booze','the-sin-of-booze','1','2006-03-28','00:00:05','Borrowed from sermonaudio.com',1,0,'2006-03-28',1);