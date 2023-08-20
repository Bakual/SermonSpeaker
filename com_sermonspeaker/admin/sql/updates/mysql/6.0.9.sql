ALTER TABLE `#__sermon_speakers`
    MODIFY `metakey` TEXT NULL;
ALTER TABLE `#__sermon_speakers`
    MODIFY `metadesc` TEXT NULL;
ALTER TABLE `#__sermon_speakers`
    MODIFY `metadata` TEXT NULL;
ALTER TABLE `#__sermon_series`
    MODIFY `series_description` MEDIUMTEXT NULL;
ALTER TABLE `#__sermon_series`
    MODIFY `metakey` TEXT NULL;
ALTER TABLE `#__sermon_series`
    MODIFY `metadesc` TEXT NULL;
ALTER TABLE `#__sermon_series`
    MODIFY `metadata` TEXT NULL;
ALTER TABLE `#__sermon_series`
    MODIFY `zip_content` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `audiofile` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `videofile` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `picture` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `notes` LONGTEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `addfile` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `addfileDesc` VARCHAR(255) NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `metakey` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `metadesc` TEXT NULL;
ALTER TABLE `#__sermon_sermons`
    MODIFY `metadata` TEXT NULL;
ALTER TABLE `#__sermon_scriptures`
    MODIFY `text` MEDIUMTEXT NULL;
