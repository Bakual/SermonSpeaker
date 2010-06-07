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
 
		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$cat['series'] = $params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = $params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));

		$this->catwhere = NULL;
		$this->cat = array();
		if ($cat['series'] != 0){
			$this->catwhere .= " AND j.catid = '".(int)$cat['series']."' \n";
			$this->cat[] = $cat['series'];
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND l.catid = '".(int)$cat['speaker']."' \n";
			$this->cat[] = $cat['speaker'];
		}

		// Get pagination request variables
		$limit = $params->get('sermonresults');
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
				."LEFT JOIN #__sermon_speakers l ON j.speaker_id = l.id \n"
				."WHERE j.published='1'".$this->catwhere;
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
		$database =& JFactory::getDBO();
		$query = 'SELECT j.id, j.speaker_id, l.name, j.series_title, j.series_description, j.published, j.ordering, j.hits, j.created_by, j.created_on, j.avatar, l.id as s_id, l.pic'
        . ' FROM #__sermon_series j'
        . ' LEFT JOIN #__sermon_speakers l ON j.speaker_id = l.id'
        . ' WHERE j.published = \'1\''
		.$this->catwhere
        . ' ORDER BY j.ordering, j.id desc, j.series_title'
        . ' LIMIT '.$this->getState('limitstart').','.$this->getState('limit'); 

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		return $rows;
	}
}