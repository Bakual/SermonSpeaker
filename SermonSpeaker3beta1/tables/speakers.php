<?php
defined('_JEXEC') or die('Restricted access');

class TableSpeakers extends JTable
{
	var $id = null;
	var $name = null;
	var $website = null;
	var $intro = null;
	var $bio = null;
	var $pic = null;
	var $published = null;
	var $ordering = null;
	var $hits = null;
	var $created_by = null;
	var $created_on = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_speakers', 'id', $db );
	}
}

?>
