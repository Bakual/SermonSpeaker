ALTER TABLE `#__sermon_sermons` ADD COLUMN `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__sermon_sermons` ADD COLUMN `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__sermon_series` ADD COLUMN `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__sermon_series` ADD COLUMN `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__sermon_speakers` ADD COLUMN `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__sermon_speakers` ADD COLUMN `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
