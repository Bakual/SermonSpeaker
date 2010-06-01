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
 
		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$cat['series'] = $params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = $params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));

		$this->catwhere = NULL;
		if ($cat['series'] != 0){
			$this->catwhere .= " AND j.catid = '".(int)$cat['series']."' \n";
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND k.catid = '".(int)$cat['speaker']."' \n";
		}

		// Get pagination request variables
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
		$query = "SELECT count(*) \n"
				."FROM #__sermon_series AS j \n"
				."LEFT JOIN #__sermon_speakers k ON j.speaker_id = k.id \n"
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
		$query = 'SELECT j.id, j.series_title , j.series_description , k.name , j.avatar'
        . ' FROM #__sermon_series j'
		. ' LEFT JOIN #__sermon_speakers k ON j.speaker_id = k.id'
        . ' WHERE j.published = 1 '
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