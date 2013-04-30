ALTER TABLE #__sermon_sermons ADD `metadata` TEXT NOT NULL;
ALTER TABLE #__sermon_series ADD `metadata` TEXT NOT NULL;
ALTER TABLE #__sermon_speakers ADD `metadata` TEXT NOT NULL;
ALTER TABLE #__sermon_sermons CHANGE `sermon_title` `title` VARCHAR(255) NOT NULL;
ALTER TABLE #__sermon_speakers CHANGE `name` `title` VARCHAR(255) NOT NULL;
ALTER TABLE #__sermon_series CHANGE `series_title` `title` VARCHAR(255) NOT NULL;
