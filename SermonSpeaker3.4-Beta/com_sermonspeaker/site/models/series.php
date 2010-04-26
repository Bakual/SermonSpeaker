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
 
		global $mainframe, $option;
		
		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$catid = $params->get('catid', JRequest::getInt('cat', ''));

		$this->catwhere = NULL;
		if ($catid != 0){
			$this->catwhere = " AND catid = '".(int)$catid."' \n";
		}

		// Get pagination request variables
//		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limit = $params->get('sermonresults');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getAvatar()
	{
		$database =& JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__sermon_series WHERE published = 1 AND avatar != ''".$this->catwhere
        . ' LIMIT '.$this->getState('limitstart').','.$this->getState('limit'); 
		$database->setQuery( $query );
		$av = $database->loadResult();

        return $av;
	}

	function getTotal()
	{
		$database =& JFactory::getDBO();
		$query = "SELECT count(*) FROM #__sermon_series WHERE published='1'".$this->catwhere;
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
		$query = 'SELECT j.id, speaker_id, l.name, series_title, series_description, j.published, j.ordering, j.hits, j.created_by, j.created_on, j.avatar'
        . ' FROM #__sermon_series j, #__sermon_speakers l'
        . ' WHERE speaker_id = l.id'
        . ' AND j.published = \'1\''
		.$this->catwhere
        . ' ORDER BY j.ordering, j.id desc, j.series_title'
        . ' LIMIT '.$this->getState('limitstart').','.$this->getState('limit'); 
		
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		return $rows;
	}
}