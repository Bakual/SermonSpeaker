<?php
function com_uninstall() {

	/* Nah!  We'll keep'em
	$database =& JFactory::getDBO();
	$query = "DROP TABLE `#__sermon_speakers`, `#__sermon_series`, `#__sermon_sermons`";
	$database->setQuery( $query );
	$database->Query();
	*/
	echo 'SermonSpeaker is uninstalled.<br>I didn\'t touch the database tables. If you want to get rid of SermonSpeaker go and delete the following tables manually:<br><ul><li>jos_sermon_speakers</li><li>jos_sermon_series</li><li>jos_sermon_sermons</li></ul>';
}
?>
