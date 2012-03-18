<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSitemap extends JModel
{
	function getSermons()
	{
		$query = "SELECT id, sermon_title, sermon_date, \n"
				."CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				."FROM #__sermon_sermons \n"
				."WHERE state = 1 \n"
				."ORDER BY sermon_date DESC"
				;

		$rows = $this->_getList($query); 

        return $rows;
	}
}