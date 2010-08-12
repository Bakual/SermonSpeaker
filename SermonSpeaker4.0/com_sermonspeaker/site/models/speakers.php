<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Sermons Model
 */
class SermonspeakerModelSpeakers extends JModel
{
	// Variablen for JPagination
	var $_total = null;
 	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
 
		$app = JFactory::getApplication();

		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
		$cat['speaker'] = $this->params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));

		$this->catwhere = NULL;
		$this->cat = array();
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND catid = '".(int)$cat['speaker']."' \n";
			$this->cat[] = $cat['speaker'];
		}

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getCat()
	{
		$database =& JFactory::getDBO();
		$cats = array_unique($this->cat);
		$title = array();
		foreach ($cats as $cat){
			$query = "SELECT title FROM #__categories WHERE id = ".$cat;
			$database->setQuery( $query );
			$title[] = $database->LoadResult();
		}
		$title = implode(' &amp; ', $title);
		return $title;
	}

	function getTotal()
	{
		$database =& JFactory::getDBO();
		$query 	= "SELECT count(*) \n"
				. "FROM #__sermon_speakers \n"
				. "WHERE published='1'"
				.$this->catwhere;
		$database->setQuery( $query );
		$total_rows = $database->LoadResult();

        return $total_rows;
	}

	function getPagination()
	{
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );

        return $this->_pagination;
	}
	
	function getData()
	{
		$database 	= &JFactory::getDBO();
		$query	= "SELECT * \n"
				. "FROM #__sermon_speakers \n"
				. "WHERE published='1' \n"
				.$this->catwhere
				. "ORDER BY ordering ASC, name";
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

		return $rows;
	}
}