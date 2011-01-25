ALTER TABLE #__sermon_sermons CHANGE `sermon_path` `audiofile` TEXT NOT NULL DEFAULT '';
ALTER TABLE #__sermon_sermons ADD `videofile` TEXT NOT NULL DEFAULT '';
