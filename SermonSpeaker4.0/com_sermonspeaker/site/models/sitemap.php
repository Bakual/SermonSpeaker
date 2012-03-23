<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSitemap extends JModel
{
	function getSermons()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, sermon_title, sermon_date');
		$query->select("CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug");
		$query->from('#__sermon_sermons');
		$query->where('state = 1');
		$query->order('sermon_date DESC');

		// Filter by cat if set
		$app	= JFactory::getApplication();
		$params	= $app->getParams();
		$cat	= (int)$params->get('cat', 0);
		if($cat){
			$query->where('catid = '.$cat);
		}

		$rows = $this->_getList($query); 

        return $rows;
	}
}