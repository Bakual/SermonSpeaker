<?php
defined('_JEXEC') or die('Restricted access');

class TableSermons extends JTable
{
	var $id = null;
	var $speaker_id = null;
	var $series_id = null;
	var $sermon_path = null;
	var $sermon_title = null;
	var $alias = null;
	var $sermon_number = null;
	var $sermon_scripture = null;
	var $custom1 = null;
	var $custom2 = null;
	var $sermon_date = null;
	var $sermon_time = null;
	var $play = null;
	var $notes = null;
	var $download = null;
	var $published = '1';
	var $ordering = null;
	var $hits = null;
	var $created_by = null;
	var $created_on = null;
	var $podcast = '1';
	var $addfile = null;
	var $addfileDesc = null;
	var $catid = null;
	var $metakey = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_sermons', 'id', $db );
	}
}

?>
