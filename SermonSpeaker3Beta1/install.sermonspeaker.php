<?php
function com_install() {

	$database =& JFactory::getDBO();
	
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
		."\n`created_on` VARCHAR(10) NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
	$query = "INSERT INTO `#__sermon_speakers` "
		."(`id`,`name`,`website`,`intro`,`bio`,`pic`,`published`,`ordering`,`hits`,`created_by`,`created_on`) VALUES"
		."(9999,'Billy Sunday','http://joomlacode.org/gf/project/sermon_speaker/','Billy Sunday died in Chicago, November 6, 1935; services were held in the Moody Memorial Church with 4,400 present. Take 15 minutes each day to listen to God talking to you; take 15 minutes each day to talk to God; take 15 minutes each day to talk to others about God.','This young convert was deeply impressed and determined to make these the rules of his life. From that day onward throughout his life he made it a rule to spend the first moments of his day alone with God and God\'s Word. Before he read a letter, looked at a paper or even read a telegram, he went first to the Bible, that the first impression of the day might be what he got directly from God.','./components/com_sermonspeaker/media/default_speaker.jpg',1,0,9,62,'1901-03-28')";
	$database->setQuery( $query );
	$database->Query();
	

	$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_series` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`speaker_id` INT NOT NULL,"
		."\n`series_title` TEXT NOT NULL,"
		."\n`series_description` TEXT NOT NULL,"
		."\n`avatar_id` INT NOT NULL,"
		."\n`published` TINYINT(1) NOT NULL,"
		."\n`ordering` int(11) NOT NULL default '0',"
		."\n`hits` INT DEFAULT '0' NOT NULL,"
		."\n`created_by` INT NOT NULL,"
		."\n`created_on` VARCHAR(10) NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
  // Now add the avatar column if it doesn't exist in the earlier table
	// install.
	$query = "describe `#__sermon_series`";
	$database->setQuery( $query );
	$database->Query();	
	$rows = $database->loadObjectList();
	$avatar_id_present = "no";

	foreach($rows as $row) {
	  foreach($row as $key => $value) {
		  if( $value == 'avatar_id') {
			  $avatar_id_present = "yes";
		  }
	  }
	}

	if($avatar_id_present == "no" ) {
	  echo "Attempting to add column avatar_id to table sermon_series...";
	  $query = "ALTER TABLE #__sermon_series ADD COLUMN avatar_id INTEGER NOT NULL DEFAULT 0";
		$database->setQuery( $query );
		$database->Query();
		if(strlen($database->getErrorMsg()) > 3){
  		echo "<span style=\"color: rgb(255, 0, 0); font-weight: bold;\">";
  		echo "<br>Failed to add the column.  Bad news.  Tell the database administrator.";
  		echo "<br>Error Message: ".$database->getErrorMsg();
  		echo "</span>";
		} else {
		  echo "<br>Wow! I did it! This is good. Now we can insert avatars. Oh boy!";
		}
	}
	
	
	$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_avatars` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`avatar_name` TEXT NOT NULL,"
		."\n`avatar_location` TEXT ,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
	$query = "INSERT INTO `#__sermon_series` "
		."(`id`,`speaker_id`,`series_title`,`series_description`,`avatar_id`,`published`,`ordering`,`hits`,`created_by`,`created_on`) VALUES"
		."(9999,9999,'General Topics','Topics of general interest.',1,1,0,0,0,'2006-03-28')";
		
	$database->setQuery( $query );
	$database->Query();

	
	$query = "INSERT INTO `#__sermon_avatars` "
		."(id,`avatar_name`,`avatar_location`) VALUES"
		."(1,'None','')";
	$database->setQuery( $query );
	$database->Query();
	
	$query = "INSERT INTO `#__sermon_avatars` "
		."(id,`avatar_name`,`avatar_location`) VALUES"
		."(2,'sharon','/components/com_sermonspeaker/media/avatars/sharon.jpg')";
	$database->setQuery( $query );
	$database->Query();
		
	$query = "CREATE TABLE IF NOT EXISTS  `#__sermon_sermons` ("
		."\n`id` INT NOT NULL AUTO_INCREMENT,"
		."\n`speaker_id` INT NOT NULL,"
		."\n`series_id` INT NOT NULL,"
		."\n`sermon_path` TEXT NOT NULL,"
		."\n`sermon_title` TEXT NOT NULL,"
		."\n`sermon_number` TEXT NOT NULL,"
		."\n`sermon_scripture` TEXT NOT NULL,"
		."\n`sermon_date` date NOT NULL,"
		."\n`sermon_time` TIME,"
		."\n`play` TINYINT(1) NOT NULL,"
		."\n`notes` LONGTEXT NOT NULL,"
		."\n`download` TINYINT(1) NOT NULL,"
		."\n`published` TINYINT(1) NOT NULL,"
		."\n`ordering` int(11) NOT NULL default '0',"
		."\n`hits` INT DEFAULT '0' NOT NULL,"
		."\n`created_by` INT NOT NULL,"
		."\n`created_on` VARCHAR(10) NOT NULL,"
		."\nPRIMARY KEY (`id`)"
		."\n)";
	$database->setQuery( $query );
	$database->Query();
	
	$query = "INSERT INTO `jos_sermon_sermons` "
		."(`id`, `speaker_id`, `series_id`, `sermon_path`, `sermon_title`, `sermon_number`, `sermon_scripture`, `sermon_date`, `sermon_time`, `play`, `notes`, `download`, `published`, `ordering`, `hits`, `created_by`, `created_on`) VALUES "
 		."(9999, 9999, 9999, '/components/com_sermonspeaker/media/default_sermon.mp3', 'The Sin of Booze', '1', 'none', '2006-03-28', '00:00:05', 0, 'Borrowed from sermonaudio.com', 1, 1, 0, 0, 62, '2006-03-28')";
  
  $database->setQuery( $query );
	$database->Query();

	echo 'Succesfully Installed.';
}
?>
