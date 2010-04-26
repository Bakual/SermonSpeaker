<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Series Model
 */
class SermonspeakerModelSeriessermon extends JModel
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
			$this->catwhere = " AND j.catid = '".(int)$catid."' \n";
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

	function getTotal()
	{
		$database =& JFactory::getDBO();
		$query = "SELECT count(*) FROM #__sermon_series AS j WHERE published='1'".$this->catwhere;
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
		$query = 'SELECT j.id, j.series_title , j.series_description , k.name , j.avatar'
        . ' FROM #__sermon_series j'
		. ' LEFT JOIN #__sermon_speakers k ON j.speaker_id = k.id'
        . ' WHERE j.published = 1 '
        . ' AND k.published = 1 '
		.$this->catwhere
        . ' ORDER BY j.ordering , j.id desc '
        . ' LIMIT '.$this->getState('limitstart').','.$this->getState('limit'); 
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		return $rows;
	}

	function getSermons($serieid)
	{
		$database =& JFactory::getDBO();
		$query	= "SELECT sermon_path, sermon_title, sermon_number, notes, sermon_date, addfile, addfileDesc \n"
				. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. " FROM #__sermon_sermons \n"
				. " WHERE series_id=".$serieid." \n"
				. " AND published = \"1\" \n"
				. " ORDER BY ordering, (sermon_number+0) DESC, sermon_date DESC";
		$database->setQuery( $query );
		$sermons = $database->loadObjectList();
		
		return $sermons;
	}
}