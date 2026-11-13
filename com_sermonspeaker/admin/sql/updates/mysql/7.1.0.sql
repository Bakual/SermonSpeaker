ALTER TABLE `#__sermon_speakers`   ENGINE=InnoDB;
ALTER TABLE `#__sermon_series`     ENGINE=InnoDB;
ALTER TABLE `#__sermon_sermons`    ENGINE=InnoDB;
ALTER TABLE `#__sermon_scriptures`
    ENGINE=InnoDB,
    ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ANALYZE TABLE `#__sermon_speakers`, `#__sermon_series`, `#__sermon_sermons`, `#__sermon_scriptures`;

UPDATE `#__sermon_sermons` SET `created` = IF(`sermon_date` >= '1000-01-01', `sermon_date`, '2000-01-01 00:00:00') WHERE `created` < '1000-01-01';
UPDATE `#__sermon_sermons` SET `modified` = `created` WHERE `modified` < '1000-01-01';

ALTER TABLE `#__sermon_sermons`
    MODIFY `sermon_date` DATETIME NULL DEFAULT NULL,
    MODIFY `publish_up` DATETIME NULL DEFAULT NULL,
    MODIFY `publish_down` DATETIME NULL DEFAULT NULL,
    MODIFY `checked_out_time` DATETIME NULL DEFAULT NULL,
    MODIFY `created` DATETIME NOT NULL,
    MODIFY `modified` DATETIME NOT NULL;

UPDATE `#__sermon_sermons` SET `sermon_date` = NULL WHERE `sermon_date` < '1000-01-01';
UPDATE `#__sermon_sermons` SET `publish_up` = NULL WHERE `publish_up` < '1000-01-01';
UPDATE `#__sermon_sermons` SET `publish_down` = NULL WHERE `publish_down` < '1000-01-01';
UPDATE `#__sermon_sermons` SET `checked_out_time` = NULL WHERE `checked_out_time` < '1000-01-01';

UPDATE `#__sermon_series` SET `created` = '2000-01-01 00:00:00' WHERE `created` IS NULL OR `created` < '1000-01-01';
UPDATE `#__sermon_series` SET `modified` = `created` WHERE `modified` < '1000-01-01';

ALTER TABLE `#__sermon_series`
    MODIFY `zip_created` DATETIME NULL DEFAULT NULL,
    MODIFY `publish_up` DATETIME NULL DEFAULT NULL,
    MODIFY `publish_down` DATETIME NULL DEFAULT NULL,
    MODIFY `checked_out_time` DATETIME NULL DEFAULT NULL,
    MODIFY `created` DATETIME NOT NULL,
    MODIFY `modified` DATETIME NOT NULL;

UPDATE `#__sermon_series` SET `zip_created` = NULL WHERE `zip_created` < '1000-01-01';
UPDATE `#__sermon_series` SET `publish_up` = NULL WHERE `publish_up` < '1000-01-01';
UPDATE `#__sermon_series` SET `publish_down` = NULL WHERE `publish_down` < '1000-01-01';
UPDATE `#__sermon_series` SET `checked_out_time` = NULL WHERE `checked_out_time` < '1000-01-01';

UPDATE `#__sermon_speakers` SET `created` = '2000-01-01 00:00:00' WHERE `created` IS NULL OR `created` < '1000-01-01';
UPDATE `#__sermon_speakers` SET `modified` = `created` WHERE `modified` < '1000-01-01';

ALTER TABLE `#__sermon_speakers`
    MODIFY `publish_up` DATETIME NULL DEFAULT NULL,
    MODIFY `publish_down` DATETIME NULL DEFAULT NULL,
    MODIFY `checked_out_time` DATETIME NULL DEFAULT NULL,
    MODIFY `created` DATETIME NOT NULL,
    MODIFY `modified` DATETIME NOT NULL;

UPDATE `#__sermon_speakers` SET `publish_up` = NULL WHERE `publish_up` < '1000-01-01';
UPDATE `#__sermon_speakers` SET `publish_down` = NULL WHERE `publish_down` < '1000-01-01';
UPDATE `#__sermon_speakers` SET `checked_out_time` = NULL WHERE `checked_out_time` < '1000-01-01';

