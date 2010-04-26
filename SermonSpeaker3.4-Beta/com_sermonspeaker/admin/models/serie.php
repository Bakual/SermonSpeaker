<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSerie extends JModel
{
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

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
}