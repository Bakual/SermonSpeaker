<?php
function com_install() {
	$database =& JFactory::getDBO();
	
	//first check if Sermonspeaker tables are already present; if they are we don't need to insert the demo sermon again...
	$query = "SELECT id FROM `#__sermon_speakers` LIMIT 1";
	$database->setQuery( $query );
	$database->Query();
	$result = $database->loadResult();
	if (!$result) {$demo = 1;} else {$demo = 0;}
  
	//create speakers table
	$query = "CREATE TABLE IF NOT EXISTS `#__sermon_speakers` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`name` TEXT NOT NULL,"
		."\n`website` TEXT,"
		."\n`intro` TEXT,"
		."\n`bio` LONGTEXT,"
		."\n`pic` TEXT,"
		."\n`published` tinyint(1) NOT NULL default '0',"
		."\n`ordering` int(11) NOT NULL default '0',"
		."\n`hits` INT DEFAULT '0' NOT NULL,"
		."\n`created_by` INT NOT NULL,"
		."\n`created_on` DATETIME NULL,"
		."\n`catid` INT NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
	//create series table
	$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_series` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`series_title` TEXT NOT NULL,"
		."\n`series_description` TEXT NOT NULL,"
		."\n`published` TINYINT(1) NOT NULL,"
		."\n`ordering` int(11) NOT NULL default '0',"
		."\n`hits` INT DEFAULT '0' NOT NULL,"
		."\n`created_by` INT NOT NULL,"
		."\n`created_on` DATETIME NULL,"
		."\n`avatar` text,"
		."\n`catid` INT NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();

	// create sermon table
	$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_sermons` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`speaker_id` INT NOT NULL,"
		."\n`series_id` INT NOT NULL,"
		."\n`sermon_path` TEXT NOT NULL,"
		."\n`sermon_title` TEXT NOT NULL,"
		."\n`alias` TEXT NOT NULL,"
		."\n`sermon_number` TEXT NOT NULL,"
		."\n`sermon_scripture` TEXT NOT NULL,"
		."\n`custom1` TEXT NOT NULL,"
		."\n`custom2` TEXT NOT NULL,"
		."\n`sermon_date` date NOT NULL,"
		."\n`sermon_time` TIME,"
		."\n`play` TINYINT(1) NOT NULL,"
		."\n`notes` LONGTEXT NOT NULL,"
		."\n`download` TINYINT(1) NOT NULL,"
		."\n`published` TINYINT(1) NOT NULL,"
		."\n`ordering` int(11) NOT NULL default '0',"
		."\n`hits` INT DEFAULT '0' NOT NULL,"
		."\n`created_by` INT NOT NULL,"
		."\n`created_on` DATETIME NULL,"
		."\n`podcast` tinyint(1) NOT NULL default '1',"
		."\n`addfile` text NOT NULL,"
		."\n`addfileDesc` text NOT NULL,"
		."\n`catid` INT NOT NULL,"
		."\n`metakey` TEXT NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
  
	// Check the tables for completeness
	$fields = $database->getTableFields('#__sermon_series');
	$series = $fields['#__sermon_series'];
	$fields = $database->getTableFields('#__sermon_sermons');
	$sermons = $fields['#__sermon_sermons'];
	$fields = $database->getTableFields('#__sermon_speakers');
	$speakers = $fields['#__sermon_speakers'];

	// Delete speaker fields in the series table as they are taken from sermons now.
	if (array_key_exists('speaker20',$series)){ // If 20 speakers
		echo "<br>Attempting to delete speaker columns from table sermon_series...";
		$query = "ALTER TABLE #__sermon_series \n"
				."DROP COLUMN speaker_id, DROP COLUMN speaker2, DROP COLUMN speaker3, DROP COLUMN speaker4, \n"
				."DROP COLUMN speaker5, DROP COLUMN speaker6, DROP COLUMN speaker7, DROP COLUMN speaker8, \n"
				."DROP COLUMN speaker9, DROP COLUMN speaker10, DROP COLUMN speaker11, DROP COLUMN speaker12, \n"
				."DROP COLUMN speaker13, DROP COLUMN speaker14, DROP COLUMN speaker15, DROP COLUMN speaker16, \n"
				."DROP COLUMN speaker17, DROP COLUMN speaker18, DROP COLUMN speaker19, DROP COLUMN speaker20";
		$database->setQuery($query);
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to drop the column.  Everything will still work, you just got to many fields in your series table.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Great! I did it! No longer used database fields are gone!.";
		}
	} elseif (array_key_exists('speaker3',$series)){ // if 3 speakers
		echo "<br>Attempting to delete speaker columns from table sermon_series...";
		$query = "ALTER TABLE #__sermon_series \n"
				."DROP COLUMN speaker_id, DROP COLUMN speaker2, DROP COLUMN speaker3";
		$database->setQuery($query);
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to drop the column.  Everything will still work, you just got to many fields in your series table.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Great! I did it! No longer used database fields are gone!.";
		}
	}

	// Add catid column if it doesn't exist in the series table
	if (!array_key_exists('catid',$series)) {
		echo "<br>Attempting to add new catid column to table sermon_series...";
		$query = "ALTER TABLE #__sermon_series ADD COLUMN catid INTEGER NOT NULL DEFAULT 0";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the column.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Wow! I did it! This is good. Now you can have categories for the series. Oh boy!";
		}
	}
	
	// Add avatar column if it doesn't exist in the series table
	if (!array_key_exists('avatar',$series)) {
		echo "<br>Attempting to add new avatar column to table sermon_series...";
		$query = "ALTER TABLE #__sermon_series ADD COLUMN avatar TEXT";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the column. Bad news. Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Wow! I did it! This is good. Now we will get rid of the table avatars for the series.";
		}
	}
	
	// Populate the new avatar column with the content of the old avatar_id -> avatar_location if present
	if (array_key_exists('avatar_id',$series)) {
		echo "<br>Attempting to convert old avatars to new avatar column...";
		$query = "SELECT a.id, b.avatar_location FROM #__sermon_series a LEFT JOIN #__sermon_avatars b ON a.avatar_id = b.id WHERE a.avatar_id != '1'";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$error = false;
		if (count($rows)){
			foreach ($rows as $row){
				$query = "UPDATE #__sermon_series SET avatar = '".$row->avatar_location."' WHERE id = '".$row->id."' ";
				$database->setQuery($query);
				$database->Query();
			}
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to convert all avatars. Bad news. Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
				$error = true;
			} else {
				echo "<br>That worked well. All avatars are converted!";
			}
		} else {
			echo "<br>Nothing to convert. Avatars weren't used so far!";
		}
		if (!$error){
			// drop the avatars table if converting was successful
			echo "<br>Attempting to drop old avatars table...";
			$query = "DROP TABLE #__sermon_avatars";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to drop table. Bad news. Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				// drop the avatar_id from series table if converting was successful
				echo "<br>Attempting to drop old avatar_id...";
				$query = "ALTER TABLE #__sermon_series DROP COLUMN avatar_id";
				$database->setQuery($query);
				$database->Query();
				if(strlen($database->getErrorMsg()) > 3){
					echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
					echo "<br>Failed to drop avatar_id. Bad news. Tell the database administrator.";
					echo "<br>Error Message: ".$database->getErrorMsg();
					echo "</span>";
				} else {
					echo "<br>Wow! I did it! Everything is cleaned up now! Rejoice!";
				}
			}
		}
	}
	
	// Add catid column if it doesn't exist in the speakers table
	if (!array_key_exists('catid',$speakers)) {
		echo "<br>Attempting to add new catid column to table sermon_speakers...";
		$query = "ALTER TABLE #__sermon_speakers ADD COLUMN catid INTEGER NOT NULL DEFAULT 0";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the column.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Wow! I did it! This is good. Now you can have categories for the speakers.";
		}
	}
	
	// Add catid column if it doesn't exist in the sermons table
	if (!array_key_exists('catid',$sermons)) {
		echo "<br>Attempting to add new catid column to table sermon_sermons...";
		$query = "ALTER TABLE #__sermon_sermons ADD COLUMN catid INTEGER NOT NULL DEFAULT 0";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the column.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Wow! I did it! This is good. Now you can have categories for the sermons.";
		}
	}
	
	// Add alias column if it doesn't exist in the sermons table
	if (!array_key_exists('alias',$sermons)) {
		echo "<br>Attempting to add new alias column to table sermon_sermons...";
		$query = "ALTER TABLE #__sermon_sermons ADD COLUMN alias VARCHAR(255) NOT NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the column.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Wow! I did it! This is good. Now you can have alias for the sermons. Oh boy!";
		}
	}
	
	// Add custom columns if they don't exist in the sermons table
	if (!array_key_exists('custom1',$sermons)) {
		echo "<br>Attempting to add new custom columns to table sermon_sermons...";
		$query = "ALTER TABLE #__sermon_sermons ADD COLUMN custom1 VARCHAR(255) NOT NULL, ADD COLUMN custom2 VARCHAR(255) NOT NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the columns.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>I did it! Now you can have customized columns for the sermons.";
		}
	}
	
	// Add metakey and metadesc column if it doesn't exist in the sermons table
	if (!array_key_exists('metakey',$sermons)) {
		echo "<br>Attempting to add new metakey and metadesc columns to table sermon_sermons...";
		$query = "ALTER TABLE #__sermon_sermons ADD COLUMN metakey text NOT NULL, ADD metadesc text NOT NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to add the columns.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>I did it! Now you have metakey and metadesc columns for the sermons.";
		}
	}
	
	// Change database field created_on from varchar() to datetime
	if ($sermons['created_on'] == 'varchar'){
		echo "<br>Attempting to change the type of the field created_on in table sermon_sermons...";
		$query = "ALTER TABLE #__sermon_sermons CHANGE created_on created_on DATETIME NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to change fieldtype.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Done! Created_on now holds DATETIME format";
		}
	}
	if ($series['created_on'] == 'varchar'){
		echo "<br>Attempting to change the type of the field created_on in table sermon_series...";
		$query = "ALTER TABLE #__sermon_series CHANGE created_on created_on DATETIME NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to change fieldtype.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Done! Created_on now holds DATETIME format";
		}
	}
	if ($speakers['created_on'] == 'varchar'){
		echo "<br>Attempting to change the type of the field created_on in table sermon_speakers...";
		$query = "ALTER TABLE #__sermon_speakers CHANGE created_on created_on DATETIME NULL";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
			echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
			echo "<br>Failed to change fieldtype.  Bad news.  Tell the database administrator.";
			echo "<br>Error Message: ".$database->getErrorMsg();
			echo "</span>";
		} else {
			echo "<br>Done! Created_on now holds DATETIME format";
		}
	}

	//Add the demo sermon if needed
	if ($demo == 1) {
		//Speaker
		$query = "INSERT INTO `#__sermon_speakers` "
			."(`id`,`name`,`website`,`intro`,`bio`,`pic`,`published`,`ordering`,`hits`,`created_by`,`created_on`) VALUES"
			."(9999,'Billy Sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','components/com_sermonspeaker/media/default_speaker.jpg',1,0,9,62,'1901-03-28')";
		$database->setQuery( $query );
		$database->Query();
  
		//Series
		$query = "INSERT INTO `#__sermon_series` "
			."(`id`, `series_title`, `series_description`, `published`, `ordering`, `hits`, `created_by`, `created_on`, `speaker2`, `speaker3`) VALUES"
			."(9999, 'General Topics', 'Topics of general interest.', 1, 0, 0, 0, '2006-03-28', 0, 0)";
		$database->setQuery( $query );
		$database->Query();
  	
		//Sermon
		$query = "INSERT INTO `#__sermon_sermons` "
		."(`id`, `speaker_id`, `series_id`, `sermon_path`, `sermon_title`, `sermon_number`, `sermon_scripture`, `sermon_date`, `sermon_time`, `play`, `notes`, `download`, `published`, `ordering`, `hits`, `created_by`, `created_on`, `podcast`, `addfile`, `addfileDesc`) VALUES"
		."(9999, 9999, 9999, '/components/com_sermonspeaker/media/default_sermon.mp3', 'The Sin of Booze', '1', 'none', '2006-03-28', '00:00:05', 0, 'Borrowed from sermonaudio.com', 1, 1, 0, 0, 62, '2006-03-28', 1, '', '')";
		$database->setQuery( $query );
		$database->Query();
	}
	
	$msg = '<table width="100%"><tr><td bgcolor="SpringGreen"><center><b>Successfull installed!<br>Please check and save the settings to apply them</b><br>Don\'t forget to upgrade the associated modules as well or they will generate errors on your site!</center></td></tr>';
	
	// check for old configfiles
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'config.sermonspeaker.php') || file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'sermoncastconfig.sermonspeaker.php')) { 
		$msg .= '<tr><td bgcolor="salmon"><center><strong>An old configfile was found! To see how to migrate it click below...</strong></center></td></tr>';
		$msg .= '<tr align="center"><td bgcolor="salmon">';
		$msg .= '<center><form action="index.php?option=com_sermonspeaker&view=main" method="post" name="adminForm">';
		$msg .= '<input type="submit" value="Migrate!">';
		$msg .= '</center></form></td></tr>';
	}
	$msg .= '<tr><td>';
	$msg .= '</table>';
	
	// cleaning up old files
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.sermonspeaker.html.php')){ 
		$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.sermonspeaker.html.php';
	}
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.sermonspeaker.php')){ 
		$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.sermonspeaker.php';
	}
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'toolbar.sermonspeaker.html.php')){ 
		$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'toolbar.sermonspeaker.html.php';
	}
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'toolbar.sermonspeaker.php')){ 
		$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'toolbar.sermonspeaker.php';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermoncast.class.php')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermoncast.class.php';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermoncast.php')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermoncast.php';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'controlpanel.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'controlpanel.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'help.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'help.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'sermon.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'sermon.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'series.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'series.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'reset.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'reset.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'speakers.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'speakers.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'stats.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'stats.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'upload.png')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'images'.DS.'upload.png';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'feedcreator.class.php')){ 
		$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'feedcreator.class.php';
	}
	if (isset($files)){
		JImport('joomla.filesystem.file');
		JFile::Delete($files);
	}
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.language')){ 
		$dirs[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.language';
	}
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.help')){ 
		$dirs[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'admin.help';
	}
	if (is_dir(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'client.language')){ 
		$dirs[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'client.language';
	}
	if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast')){ 
		$dirs[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast';
	}
	if (isset($dirs)){
		JImport('joomla.filesystem.folder');
		foreach ($dirs as $dir){
			JFolder::Delete($dir);
		}
	}
	echo $msg;
	return;
}
?>