ALTER TABLE #__sermon_sermons CHANGE `sermon_path` `audiofile` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons ADD `videofile` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons ADD `picture` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons CHANGE `sermon_date` `sermon_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
