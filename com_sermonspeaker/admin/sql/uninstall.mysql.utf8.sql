DROP TABLE IF EXISTS `#__sermon_speakers`;
DROP TABLE IF EXISTS `#__sermon_series`;
DROP TABLE IF EXISTS `#__sermon_sermons`;
DROP TABLE IF EXISTS `#__sermon_scriptures`;
DELETE
FROM `#__ucm_content`
WHERE `core_type_alias` LIKE 'com_sermonspeaker.%';
DELETE
FROM `#__contentitem_tag_map`
WHERE `type_alias` = 'com_sermonspeaker.%';
DELETE a
FROM `#__ucm_base` AS a
         INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id
WHERE b.type_alias LIKE 'com_sermonspeaker.%';
DELETE FROM `#__history`
WHERE `item_id` LIKE 'com_sermonspeaker.%';
DELETE
FROM `#__categories`
WHERE `extension` LIKE 'com_sermonspeaker.%';
DELETE
FROM `#__assets`
WHERE `name` LIKE 'com_sermonspeaker.%';
