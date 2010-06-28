<?php
defined('_JEXEC') or die('Restricted access');

class TableSeries extends JTable
{
	var $id = null;
	var $series_title = null;
	var $series_description = null;
	var $published = null;
	var $ordering = null;
	var $hits = null;
	var $created_by = null;
	var $created_on = null;
	var $catid = null;
	var $avatar = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_series', 'id', $db );
	}
}

?>
