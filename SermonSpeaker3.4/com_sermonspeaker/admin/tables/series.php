<?php
defined('_JEXEC') or die('Restricted access');

class TableSeries extends JTable
{
	var $id = null;
	var $speaker_id = null;
	var $series_title = null;
	var $series_description = null;
	var $published = null;
	var $ordering = null;
	var $hits = null;
	var $created_by = null;
	var $created_on = null;
	var $speaker2 = null;
	var $speaker3 = null;
	var $speaker4 = null;
	var $speaker5 = null;
	var $speaker6 = null;
	var $speaker7 = null;
	var $speaker8 = null;
	var $speaker9 = null;
	var $speaker10 = null;
	var $speaker11 = null;
	var $speaker12 = null;
	var $speaker13 = null;
	var $speaker14 = null;
	var $speaker15 = null;
	var $speaker16 = null;
	var $speaker17 = null;
	var $speaker18 = null;
	var $speaker19 = null;
	var $speaker20 = null;
	var $catid = null;
	var $avatar = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_series', 'id', $db );
	}
}

?>
