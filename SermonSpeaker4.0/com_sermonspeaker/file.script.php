<?php
class Com_SermonspeakerInstallerScript {

	function install($parent) {
		$database =& JFactory::getDBO();
		
		//first check if Sermonspeaker tables are already present; if they are we don't need to insert the demo sermon again...
		$query = "SELECT id FROM `#__sermon_sermons` LIMIT 1";
		$database->setQuery($query);
		$database->Query();
		$result = $database->loadResult();
		if (!$result) {$demo = 1;} else {$demo = 0;}

		// Apply Database changes
		$this->migrate();
		
		//Add the demo sermon if needed
		if ($demo == 1) {
			//Speaker
			$query = "INSERT INTO `#__sermon_speakers` "
				."(`id`,`name`,`website`,`intro`,`bio`,`pic`,`state`,`ordering`,`created_by`,`created`) VALUES"
				."(1,'Billy Sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','components/com_sermonspeaker/media/default_speaker.jpg',1,1,62,'2006-03-28')";
			$database->setQuery($query);
			$database->Query();
	  
			//Series
			$query = "INSERT INTO `#__sermon_series` "
				."(`id`, `series_title`, `series_description`, `state`, `ordering`, `created_by`, `created`) VALUES"
				."(1, 'General Topics', 'Topics of general interest.', 1, 1, 62, '2006-03-28')";
			$database->setQuery($query);
			$database->Query();
		
			//Sermon
			$query = "INSERT INTO `#__sermon_sermons` "
			."(`id`, `speaker_id`, `series_id`, `sermon_path`, `sermon_title`, `sermon_number`, `sermon_date`, `sermon_time`, `notes`, `ordering`, `created_by`, `created`, `state`) VALUES"
			."(1, 1, 1, '/components/com_sermonspeaker/media/default_sermon.mp3', 'The Sin of Booze', '1', '2006-03-28', '00:00:05', 'Borrowed from sermonaudio.com', 1, 62, '2006-03-28', 1)";
			$database->setQuery($query);
			$database->Query();
		}

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

		// Execute the database upgrades
		$this->migrate();
		
		$msg = '<table width="100%"><tr><td bgcolor="SpringGreen"><center><b>Successfull installed!<br>Please check and save the settings to apply them</b><br>Don\'t forget to upgrade the associated modules as well or they will generate errors on your site!</center></td></tr>';
		
		echo $msg;
		return;
	}

	function preflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_PREFLIGHT', $type);
	}

	function postflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type);
	}

	protected function migrate() {
		$database =& JFactory::getDBO();
		
		//create speakers table
		$query = "CREATE TABLE IF NOT EXISTS `#__sermon_speakers` ("
			."\n`id` INT NOT NULL AUTO_INCREMENT,"
			."\n`name` TEXT NOT NULL,"
			."\n`alias` TEXT NOT NULL,"
			."\n`website` TEXT,"
			."\n`intro` TEXT,"
			."\n`bio` LONGTEXT,"
			."\n`pic` TEXT,"
			."\n`state` tinyint(3) NOT NULL default '0',"
			."\n`ordering` int(11) NOT NULL default '0',"
			."\n`hits` INT DEFAULT '0' NOT NULL,"
			."\n`created_by` INT NOT NULL,"
			."\n`created` DATETIME NULL,"
			."\n`catid` INT NOT NULL,"
			."\n`metakey` TEXT NOT NULL,"
			."\n`metadesc` TEXT NOT NULL,"
			."\nPRIMARY KEY (`id`)"
			."\n)";
		$database->setQuery( $query );
		$database->Query();
		
		//create series table
		$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_series` ("
			."\n`id` INT NOT NULL AUTO_INCREMENT,"
			."\n`series_title` TEXT NOT NULL,"
			."\n`alias` TEXT NOT NULL,"
			."\n`series_description` TEXT NOT NULL,"
			."\n`state` TINYINT(3) NOT NULL,"
			."\n`ordering` int(11) NOT NULL default '0',"
			."\n`hits` INT DEFAULT '0' NOT NULL,"
			."\n`created_by` INT NOT NULL,"
			."\n`created` DATETIME NULL,"
			."\n`avatar` text,"
			."\n`catid` INT NOT NULL,"
			."\n`metakey` TEXT NOT NULL,"
			."\n`metadesc` TEXT NOT NULL,"
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
			."\n`sermon_number` INT NULL,"
			."\n`sermon_scripture` TEXT NOT NULL,"
			."\n`custom1` TEXT NOT NULL,"
			."\n`custom2` TEXT NOT NULL,"
			."\n`sermon_date` date NOT NULL,"
			."\n`sermon_time` TIME,"
			."\n`notes` LONGTEXT NOT NULL,"
			."\n`state` TINYINT(3) NOT NULL,"
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
		
	  
		// Check the tables for completenes
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
		
		// Rename `published` column to `state` if it does exist in the series table
		if (array_key_exists('published', $series)) {
			echo "<br>Attempting to rename `published` column in table sermon_series...";
			$query = "ALTER TABLE #__sermon_series CHANGE COLUMN `published` `state` TINYINT(3) NOT NULL";
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
		
		// Add alias, metakey and metadesc columns to the series table if they don't exist
		if (!array_key_exists('alias', $series)) {
			echo "<br>Attempting to add `alias`, `metakey` and `metadesc` columns to table sermon_series...";
			$query = "ALTER TABLE #__sermon_series ADD (`alias` TEXT NOT NULL, `metakey` TEXT NOT NULL, `metadesc` TEXT NOT NULL)";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to add the columns.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully added these colums! We're going to use this for improved SEF.";
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
		
		// Rename `published` column to `state` if it does exist in the speakers table
		if (array_key_exists('published', $speakers)) {
			echo "<br>Attempting to rename `published` column in table sermon_speakers...";
			$query = "ALTER TABLE #__sermon_speakers CHANGE COLUMN `published` `state` TINYINT(3) NOT NULL";
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
		
		// Add alias, metakey and metadesc columns to the speakers table if they don't exist
		if (!array_key_exists('alias', $speakers)) {
			echo "<br>Attempting to add `alias`, `metakey` and `metadesc` columns to table sermon_speakers...";
			$query = "ALTER TABLE #__sermon_speakers ADD (`alias` TEXT NOT NULL, `metakey` TEXT NOT NULL, `metadesc` TEXT NOT NULL)";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to add the columns.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully added these colums! We're going to use this for improved SEF.";
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
		
		// Rename `published` column to `state` if it does exist in the sermons table
		if (array_key_exists('published', $sermons)) {
			echo "<br>Attempting to rename `published` column in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons CHANGE COLUMN `published` `state` TINYINT(3) NOT NULL";
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
		
		// Change database field sermon_number from text to integer
		if ($sermons['sermon_number'] == 'text'){
			echo "<br>Attempting to change the type of the field sermon_number in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons CHANGE sermon_number sermon_number INT NULL";
			$database->setQuery( $query );
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to change fieldtype.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Done! Sermon_number now holds INT format";
			}
		}

		// Remove `play` column if it does exist in the sermons table
		if (array_key_exists('play', $sermons)) {
			echo "<br>Attempting to remove `play` column in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons DROP COLUMN `play`";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully dropped the column!";
			}
		}
		
		// Remove `download` column if it does exist in the sermons table
		if (array_key_exists('download', $sermons)) {
			echo "<br>Attempting to remove `download` column in table sermon_sermons...";
			$query = "ALTER TABLE #__sermon_sermons DROP COLUMN `download`";
			$database->setQuery($query);
			$database->Query();
			if(strlen($database->getErrorMsg()) > 3){
				echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
				echo "<br>Failed to rename the column.  Bad news.  Tell the database administrator.";
				echo "<br>Error Message: ".$database->getErrorMsg();
				echo "</span>";
			} else {
				echo "<br>Successfully dropped the column!";
			}
		}

		return;
	}
}