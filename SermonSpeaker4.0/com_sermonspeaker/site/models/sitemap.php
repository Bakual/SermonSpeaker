<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSitemap extends JModel
{
	function __construct()
	{
		parent::__construct();

		$this->db =& JFactory::getDBO();
	}

	function getSpeakers()
	{
		$query = "SELECT id, name, hits FROM #__sermon_speakers \n"
				."ORDER BY id"
				;
				
		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSeries()
	{
		$query = "SELECT id, series_title, hits FROM #__sermon_series \n"
				."ORDER BY id"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSermons()
	{
		$query = "SELECT id, sermon_title, sermon_date, \n"
				."CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				."FROM #__sermon_sermons \n"
				."ORDER BY sermon_date DESC"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}
}