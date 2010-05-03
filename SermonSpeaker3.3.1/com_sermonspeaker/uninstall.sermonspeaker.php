<?php
function com_uninstall() {

	/* Nah!  We'll keep'em
	$database =& JFactory::getDBO();
	$query = "DROP TABLE `#__sermon_speakers`, `#__sermon_series`, `#__sermon_sermons`";
	$database->setQuery( $query );
	$database->Query();
	*/
	echo "Uninstalled.";
}
?>
