<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Series Model
 */
class SermonspeakerModelSerie extends JModel
{
	// Variablen for JPagination
	var $_total = null;
 	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
 
		$app = JFactory::getApplication();

		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$this->id		= JRequest::getInt('id',$params->get('serie_id'));

		// Get pagination request variables
//		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limit = $params->get('sermonresults');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get sorting order from Request and UserState
		$this->lists['order']		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order",'filter_order','sermon_date','cmd' );
		$this->lists['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order_Dir",'filter_order_Dir','DESC','word' );
	}

	function getOrder()
	{
        return $this->lists;
	}

	function _buildContentOrderBy() {
		return $this->lists['order'].' '.$this->lists['order_Dir'];
	}
	
	function getTotal()
	{
		$database = &JFactory::getDBO();
		$query	= "SELECT count(*) "
				. "FROM #__sermon_sermons a, #__sermon_speakers b "
				. "WHERE a.series_id='".$this->id."' "
				. "AND a.speaker_id = b.id "
				. "AND a.published='1'";
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
	
	function getSerie()
	{
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__sermon_series WHERE id='".$this->id."'";
		$database->setQuery( $query );
		$row = $database->loadObjectList();

       return $row;
	}
	
	function getData()
	{
		$orderby	= $this->_buildContentOrderBy();
		$database =& JFactory::getDBO();
		$query	= "SELECT a.*, b.name, b.pic, b.id as s_id "
				. ", CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug \n"
				. "FROM #__sermon_sermons a \n"
				. "LEFT JOIN #__sermon_speakers b ON a.speaker_id = b.id \n"
				. "WHERE a.series_id='".$this->id."' "
				. "AND a.speaker_id = b.id "
				. "AND a.published='1' "
				. "ORDER BY ".$orderby." \n"
				. "LIMIT ".$this->getState('limitstart').", ".$this->getState('limit');
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		return $rows;
	}
}