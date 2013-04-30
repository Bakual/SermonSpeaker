<?php
defined('_JEXEC') or die;

class SermonspeakerModelStatistics extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();

		$this->db = JFactory::getDBO();
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
		$query = "SELECT id, title, hits FROM #__sermon_series \n"
				."ORDER BY id"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSermons()
	{
		$query = "SELECT id, title, hits FROM #__sermon_sermons \n"
				."ORDER BY id"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}
}