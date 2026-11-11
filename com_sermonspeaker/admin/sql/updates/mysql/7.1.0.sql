ALTER TABLE `#__sermon_speakers`   ENGINE=InnoDB;
ALTER TABLE `#__sermon_series`     ENGINE=InnoDB;
ALTER TABLE `#__sermon_sermons`    ENGINE=InnoDB;
ALTER TABLE `#__sermon_scriptures`
    ENGINE=InnoDB,
    ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ANALYZE TABLE `#__sermon_speakers`, `#__sermon_series`, `#__sermon_sermons`, `#__sermon_scriptures`;
