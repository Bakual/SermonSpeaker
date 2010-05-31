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
 
		global $option;
		
		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
		$cat['speaker'] = $this->params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));

		$this->catwhere = NULL;
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND catid = '".(int)$cat['speaker']."' \n";
		}


		// Get pagination request variables
		$limit = $this->params->get('sermonresults');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
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
				. "ORDER BY ordering ASC, name \n"
				. "LIMIT ".$this->getState('limitstart').','.$this->getState('limit');
		$database->setQuery( $query );
		$rows	= $database->loadObjectList();

		return $rows;
	}
}