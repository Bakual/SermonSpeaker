<?php
class Com_SermonspeakerInstallerScript {

	function install($parent) {
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
			."\n`created` DATETIME NULL,"
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
			."\n`created` DATETIME NULL,"
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
			."\n`created` DATETIME NULL,"
			."\n`podcast` tinyint(1) NOT NULL default '1',"
			."\n`addfile` text NOT NULL,"
			."\n`addfileDesc` text NOT NULL,"
			."\n`catid` INT NOT NULL,"
			."\n`metakey` TEXT NOT NULL,"
			."\n`metadesc` TEXT NOT NULL,"
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

		// Rename `created_on` column to `created` if it does exist in the series table
		if (array_key_exists('created_on', $series)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_series...";
			$query = "ALTER TABLE #__sermon_series CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		// Rename `created_on` column to `created` if it does exist in the speakers table
		if (array_key_exists('created_on', $speakers)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_speakers...";
			$query = "ALTER TABLE #__sermon_speakers CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		// Rename `created_on` column to `created` if it does exist in the sermons table
		if (array_key_exists('created_on', $sermons)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		//Add the demo sermon if needed
		if ($demo == 1) {
			//Speaker
			$query = "INSERT INTO `#__sermon_speakers` "
				."(`id`,`name`,`website`,`intro`,`bio`,`pic`,`published`,`ordering`,`hits`,`created_by`,`created`) VALUES"
				."(9999,'Billy Sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','components/com_sermonspeaker/media/default_speaker.jpg',1,0,9,62,'1901-03-28')";
			$database->setQuery( $query );
			$database->Query();
	  
			//Series
			$query = "INSERT INTO `#__sermon_series` "
				."(`id`, `series_title`, `series_description`, `published`, `ordering`, `hits`, `created_by`, `created`, `speaker2`, `speaker3`) VALUES"
				."(9999, 'General Topics', 'Topics of general interest.', 1, 0, 0, 0, '2006-03-28', 0, 0)";
			$database->setQuery( $query );
			$database->Query();
		
			//Sermon
			$query = "INSERT INTO `#__sermon_sermons` "
			."(`id`, `speaker_id`, `series_id`, `sermon_path`, `sermon_title`, `sermon_number`, `sermon_scripture`, `sermon_date`, `sermon_time`, `play`, `notes`, `download`, `published`, `ordering`, `hits`, `created_by`, `created`, `podcast`, `addfile`, `addfileDesc`) VALUES"
			."(9999, 9999, 9999, '/components/com_sermonspeaker/media/default_sermon.mp3', 'The Sin of Booze', '1', 'none', '2006-03-28', '00:00:05', 0, 'Borrowed from sermonaudio.com', 1, 1, 0, 0, 62, '2006-03-28', 1, '', '')";
			$database->setQuery( $query );
			$database->Query();
		}
		
		$msg = '<table width="100%"><tr><td bgcolor="SpringGreen"><center><b>Successfull installed!<br>Please check and save the settings to apply them</b><br>Don\'t forget to upgrade the associated modules as well or they will generate errors on your site!</center></td></tr>';
		
	/* Method "Upgrade" should take care of the files
		// cleaning up old files
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'sermon'.DS.'form.php')){ 
			$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'sermon'.DS.'form.php';
		}
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php')){ 
			$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php';
		}
		if (isset($files)){
			JImport('joomla.filesystem.file');
			JFile::Delete($files);
		}
		if (is_dir(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast')){ 
			$dirs[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast';
		}
		if (isset($dirs)){
			JImport('joomla.filesystem.folder');
			foreach ($dirs as $dir){
				JFolder::Delete($dir);
			}
		}
	*/
		echo $msg;
		return;
	}

	function uninstall($parent) {
		/* Nah!  We'll keep'em
		$database =& JFactory::getDBO();
		$query = "DROP TABLE `#__sermon_speakers`, `#__sermon_series`, `#__sermon_sermons`";
		$database->setQuery( $query );
		$database->Query();
		*/
		echo 'SermonSpeaker is uninstalled.<br>I didn\'t touch the database tables. If you want to get rid of SermonSpeaker go and delete the following tables manually:<br><ul><li>jos_sermon_speakers</li><li>jos_sermon_series</li><li>jos_sermon_sermons</li></ul>';
	}

	function update($parent) {
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
			."\n`created` DATETIME NULL,"
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
			."\n`created` DATETIME NULL,"
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
			."\n`created` DATETIME NULL,"
			."\n`podcast` tinyint(1) NOT NULL default '1',"
			."\n`addfile` text NOT NULL,"
			."\n`addfileDesc` text NOT NULL,"
			."\n`catid` INT NOT NULL,"
			."\n`metakey` TEXT NOT NULL,"
			."\n`metadesc` TEXT NOT NULL,"
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

		// Rename `created_on` column to `created` if it does exist in the series table
		if (array_key_exists('created_on', $series)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_series...";
			$query = "ALTER TABLE #__sermon_series CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		// Rename `created_on` column to `created` if it does exist in the speakers table
		if (array_key_exists('created_on', $speakers)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_speakers...";
			$query = "ALTER TABLE #__sermon_speakers CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		// Rename `created_on` column to `created` if it does exist in the sermons table
		if (array_key_exists('created_on', $sermons)) {
			echo "<br>Attempting to rename `created_on` column in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons CHANGE COLUMN `created_on` `created` DATETIME";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully changed the name! We're closer to the Joomla standard naming now.";
			}
		}
		
		//Add the demo sermon if needed
		if ($demo == 1) {
			//Speaker
			$query = "INSERT INTO `#__sermon_speakers` "
				."(`id`,`name`,`website`,`intro`,`bio`,`pic`,`published`,`ordering`,`hits`,`created_by`,`created`) VALUES"
				."(9999,'Billy Sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','components/com_sermonspeaker/media/default_speaker.jpg',1,0,9,62,'1901-03-28')";
			$database->setQuery( $query );
			$database->Query();
	  
			//Series
			$query = "INSERT INTO `#__sermon_series` "
				."(`id`, `series_title`, `series_description`, `published`, `ordering`, `hits`, `created_by`, `created`, `speaker2`, `speaker3`) VALUES"
				."(9999, 'General Topics', 'Topics of general interest.', 1, 0, 0, 0, '2006-03-28', 0, 0)";
			$database->setQuery( $query );
			$database->Query();
		
			//Sermon
			$query = "INSERT INTO `#__sermon_sermons` "
			."(`id`, `speaker_id`, `series_id`, `sermon_path`, `sermon_title`, `sermon_number`, `sermon_scripture`, `sermon_date`, `sermon_time`, `play`, `notes`, `download`, `published`, `ordering`, `hits`, `created_by`, `created`, `podcast`, `addfile`, `addfileDesc`) VALUES"
			."(9999, 9999, 9999, '/components/com_sermonspeaker/media/default_sermon.mp3', 'The Sin of Booze', '1', 'none', '2006-03-28', '00:00:05', 0, 'Borrowed from sermonaudio.com', 1, 1, 0, 0, 62, '2006-03-28', 1, '', '')";
			$database->setQuery( $query );
			$database->Query();
		}
		
		$msg = '<table width="100%"><tr><td bgcolor="SpringGreen"><center><b>Successfull installed!<br>Please check and save the settings to apply them</b><br>Don\'t forget to upgrade the associated modules as well or they will generate errors on your site!</center></td></tr>';
		
	/* Method "Upgrade" should take care of the files
		// cleaning up old files
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'sermon'.DS.'form.php')){ 
			$files[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'sermon'.DS.'form.php';
		}
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php')){ 
			$files[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'sermonspeaker.html.php';
		}
		if (isset($files)){
			JImport('joomla.filesystem.file');
			JFile::Delete($files);
		}
		if (is_dir(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast')){ 
			$dirs[] = JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'views'.DS.'podcast';
		}
		if (isset($dirs)){
			JImport('joomla.filesystem.folder');
			foreach ($dirs as $dir){
				JFolder::Delete($dir);
			}
		}
	*/
		echo $msg;
		return;
	}

	function preflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_PREFLIGHT', $type);
	}

	function postflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type);
	}
}