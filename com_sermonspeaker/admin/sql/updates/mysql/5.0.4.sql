ALTER TABLE `#__sermon_sermons` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__sermon_series` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__sermon_speakers` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
