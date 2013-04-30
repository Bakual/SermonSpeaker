<?php
defined('_JEXEC') or die;

class SermonspeakerModelSitemap extends JModelLegacy
{
	function getSermons()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, title, sermon_date, created');
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