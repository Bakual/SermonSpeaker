<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Series Model
 */
class SermonspeakerModelSeries extends JModel
{
	// Variablen for JPagination
	var $_total = null;
 	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
 
		$app = JFactory::getApplication();

		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$cat['series'] = $params->get('series_cat', JRequest::getInt('series_cat', ''));

		$this->catwhere = NULL;
		$this->cat = array();
		if ($cat['series'] != 0){
			$this->catwhere .= " AND j.catid = '".(int)$cat['series']."' \n";
			$this->cat[] = $cat['series'];
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
		$query = "SELECT count(*) FROM #__sermon_series j \n"
				."WHERE j.state='1'".$this->catwhere;
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
	
	function getSpeakers($series)
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT sermons.speaker_id, speakers.name, speakers.pic'
        . ' FROM #__sermon_sermons AS sermons'
        . ' LEFT JOIN #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id'
        . " WHERE sermons.state = '1'"
        . " AND speakers.state = '1'"
		. " AND sermons.series_id = '".$series."'"
        . ' GROUP BY sermons.speaker_id'
        . ' ORDER BY speakers.name';
		$db->setQuery($query);
		$speakers = $db->loadObjectList();

		return $speakers;
	}

	function getData()
	{
		$database =& JFactory::getDBO();
		$query = 'SELECT j.id, j.series_title, j.series_description, j.avatar'
        . ' FROM #__sermon_series j'
        . " WHERE j.state = '1'"
		.$this->catwhere
        . ' ORDER BY j.ordering, j.id desc, j.series_title';
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

		return $rows;
	}
}