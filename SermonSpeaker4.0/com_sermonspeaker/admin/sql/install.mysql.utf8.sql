DROP TABLE IF EXISTS `#__sermon_speakers`;
DROP TABLE IF EXISTS `#__sermon_series`;
DROP TABLE IF EXISTS `#__sermon_sermons`;
 
CREATE TABLE `#__sermon_speakers` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`website` TEXT,
	`intro` MEDIUMTEXT,
	`bio` MEDIUMTEXT,
	`pic` TEXT,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__sermon_series` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`series_title` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`series_description` MEDIUMTEXT NOT NULL,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NULL DEFAULT '0000-00-00 00:00:00',
	`avatar` TEXT,
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__sermon_sermons` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`speaker_id` INT(10) NOT NULL DEFAULT '0',
	`series_id` INT(10) NOT NULL DEFAULT '0',
	`audiofile` TEXT NOT NULL DEFAULT '',
	`videofile` TEXT NOT NULL DEFAULT '',
	`picture` TEXT NOT NULL DEFAULT '',
	`sermon_title` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`sermon_number` INT(10) NOT NULL DEFAULT '0',
	`sermon_scripture` MEDIUMTEXT NOT NULL,
	`custom1` MEDIUMTEXT NOT NULL,
	`custom2` MEDIUMTEXT NOT NULL,
	`sermon_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`sermon_time` TIME NOT NULL DEFAULT '00:00:00',
	`notes` LONGTEXT NOT NULL,
	`state` TINYINT(3) NOT NULL DEFAULT '0',
	`ordering` INT(11) NOT NULL DEFAULT '0',
	`hits` INT(10) NOT NULL DEFAULT '0',
	`created_by` INT(10) NOT NULL DEFAULT '0',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`podcast` TINYINT(1) NOT NULL DEFAULT '0',
	`addfile` TEXT NOT NULL,
	`addfileDesc` VARCHAR(255) NOT NULL,
	`catid` INT(10) NOT NULL DEFAULT '0',
	`metakey` TEXT NOT NULL,
	`metadesc` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__sermon_speakers`
	(`id`,`name`,`alias`,`website`,`intro`,`bio`,`pic`,`state`,`ordering`,`created_by`,`created`) 
	VALUES (1,'Billy Sunday','billy-sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','components/com_sermonspeaker/media/default_speaker.jpg',1,1,62,'2006-03-28');
	  
INSERT INTO `#__sermon_series`
	(`id`,`series_title`,`alias`,`series_description`,`state`,`ordering`,`created_by`,`created`) 
	VALUES (1,'General Topics','general-topics','Topics of general interest.',1,1,62,'2006-03-28');
		
INSERT INTO `#__sermon_sermons`
	(`id`,`speaker_id`,`series_id`,`audiofile`,`sermon_title`,`alias`,`sermon_number`,`sermon_date`,`sermon_time`,`notes`,`ordering`,`created_by`,`created`,`state`) 
	VALUES (1,1,1,'/components/com_sermonspeaker/media/default_sermon.mp3','The Sin of Booze','the-sin-of-booze','1','2006-03-28','00:00:05','Borrowed from sermonaudio.com',1,62,'2006-03-28',1);
