<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelStatistics extends JModel
{
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		$this->db =& JFactory::getDBO();
	}

	function getSpeakers()
	{
		$query = "SELECT * FROM #__sermon_speakers \n"
				."ORDER BY id"
				;
				
		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSeries()
	{
		$query = "SELECT * FROM #__sermon_series \n"
				."ORDER BY id"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSermons()
	{
		$query = "SELECT * FROM #__sermon_sermons \n"
				."ORDER BY id"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}
}