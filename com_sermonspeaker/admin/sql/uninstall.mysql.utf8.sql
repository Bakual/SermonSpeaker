DROP TABLE IF EXISTS `#__sermon_speakers`;
DROP TABLE IF EXISTS `#__sermon_series`;
DROP TABLE IF EXISTS `#__sermon_sermons`;
DROP TABLE IF EXISTS `#__sermon_scriptures`;
DELETE
FROM `#__ucm_content`
WHERE `core_type_alias` = 'com_sermonspeaker.sermon';
DELETE
FROM `#__ucm_content`
WHERE `core_type_alias` = 'com_sermonspeaker.speaker';
DELETE
FROM `#__ucm_content`
WHERE `core_type_alias` = 'com_sermonspeaker.serie';
DELETE
FROM `#__contentitem_tag_map`
WHERE `type_alias` = 'com_sermonspeaker.sermon';
DELETE
FROM `#__contentitem_tag_map`
WHERE `type_alias` = 'com_sermonspeaker.speaker';
DELETE
FROM `#__contentitem_tag_map`
WHERE `type_alias` = 'com_sermonspeaker.serie';
DELETE a
FROM `#__ucm_base` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.sermon';
DELETE a
FROM `#__ucm_base` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.speaker';
DELETE a
FROM `#__ucm_base` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.serie';
DELETE a
FROM `#__ucm_history` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.sermon';
DELETE a
FROM `#__ucm_history` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.speaker';
DELETE a
FROM `#__ucm_history` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias = 'com_sermonspeaker.serie';
DELETE
FROM `#__content_types`
WHERE `type_alias` = 'com_sermonspeaker.sermon';
DELETE
FROM `#__content_types`
WHERE `type_alias` = 'com_sermonspeaker.speaker';
DELETE
FROM `#__content_types`
WHERE `type_alias` = 'com_sermonspeaker.serie';
