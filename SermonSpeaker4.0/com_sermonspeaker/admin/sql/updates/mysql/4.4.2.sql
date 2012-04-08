ALTER TABLE #__sermon_sermons ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_sermons ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_sermons ADD `language` CHAR(7) NOT NULL;
ALTER TABLE #__sermon_speakers ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_speakers ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_speakers ADD `language` CHAR(7) NOT NULL;
ALTER TABLE #__sermon_series ADD `checked_out` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE #__sermon_series ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE #__sermon_series ADD `language` CHAR(7) NOT NULL;
