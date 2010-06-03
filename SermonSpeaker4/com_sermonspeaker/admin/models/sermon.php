<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSermon extends JModel
{
	function __construct()
	{
		parent::__construct();

		$this->db				= &JFactory::getDBO();
	}

	function getSpeakers()
	{
        $query = "SELECT id, name \n"
				."FROM #__sermon_speakers \n"
				."ORDER BY name"
				;
		$rows = $this->_getList($query); 

        return $rows;
	}

	function getSeries()
	{
        $query = "SELECT id, series_title \n"
				."FROM #__sermon_series \n"
				."ORDER BY id"
				;
		$rows = $this->_getList($query); 

        return $rows;
	}
}