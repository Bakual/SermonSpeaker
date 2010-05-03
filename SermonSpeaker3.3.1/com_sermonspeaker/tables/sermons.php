<?php
defined('_JEXEC') or die('Restricted access');

class TableSermons extends JTable
{
	var $id = null;
	var $speaker_id = null;
	var $series_id = null;
	var $sermon_path = null;
	var $sermon_title = null;
	var $sermon_number = null;
	var $sermon_scripture = null;
	var $sermon_date = null;
	var $sermon_time = null;
	var $play = null;
	var $notes = null;
	var $download = null;
	var $published = null;
	var $ordering = null;
	var $hits = null;
	var $created_by = null;
	var $created_on = null;
	var $podcast = null;
	var $addfile = null;
	var $addfileDesc = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_sermons', 'id', $db );
	}
}

?>
