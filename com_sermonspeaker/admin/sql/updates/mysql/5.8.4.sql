ALTER TABLE `#__sermon_scriptures`
    ADD INDEX `idx_sermon_id` (`sermon_id`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_checkout` (`checked_out`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_state` (`state`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_catid` (`catid`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_createdby` (`created_by`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_language` (`language`);
ALTER TABLE `#__sermon_speakers`
    ADD INDEX `idx_alias` (`alias`(191));
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_checkout` (`checked_out`);
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_state` (`state`);
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_catid` (`catid`);
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_createdby` (`created_by`);
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_language` (`language`);
ALTER TABLE `#__sermon_series`
    ADD INDEX `idx_alias` (`alias`(191));
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_checkout` (`checked_out`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_state` (`state`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_catid` (`catid`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_createdby` (`created_by`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_language` (`language`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_alias` (`alias`(191));
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_speaker_id` (`speaker_id`);
ALTER TABLE `#__sermon_sermons`
    ADD INDEX `idx_series_id` (`series_id`);
