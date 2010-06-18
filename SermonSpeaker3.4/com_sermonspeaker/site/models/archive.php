<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Sermons Model
 */
class SermonspeakerModelArchive extends JModel
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
		$cat['speaker'] = $params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));
		$cat['sermon'] = $params->get('sermon_cat', JRequest::getInt('sermon_cat', ''));

		$this->seriesjoin = NULL;
		$this->catwhere = NULL;
		$this->cat = array();
		if ($cat['series'] != 0){
			$this->seriesjoin = " LEFT JOIN #__sermon_series AS ss ON j.series_id = ss.id \n";
			$this->catwhere .= " AND ss.catid = '".(int)$cat['series']."' \n";
			$this->cat[] = $cat['series'];
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND k.catid = '".(int)$cat['speaker']."' \n";
			$this->cat[] = $cat['speaker'];
		}
		if ($cat['sermon'] != 0){
			$this->catwhere .= " AND j.catid = '".(int)$cat['sermon']."' \n";
			$this->cat[] = $cat['sermon'];
		}

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get sorting order from Request and UserState
		$this->lists['order']		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order",'filter_order','sermon_date','cmd' );
		$this->lists['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order_Dir",'filter_order_Dir','DESC','word' );

		$date = getDate();
		if (JRequest::getInt('year') || JRequest::getInt('month')){
			$this->year = JRequest::getInt('year', $date[year]);
			$this->month = JRequest::getInt('month', '');
		} else {
			$this->year = $params->get('year', $date[year]);
			$this->month = $params->get('month', $date[mon]);
		}
		if ($this->month){
			$this->and_m = "AND MONTH(j.sermon_date)='".$this->month."' \n";
		}
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

	function getOrder()
	{
        return $this->lists;
	}

	function _buildContentOrderBy() {
		return $this->lists['order'].' '.$this->lists['order_Dir'];
	}
	
	function getTotal()
	{
		$database =& JFactory::getDBO();
		$query	= "SELECT count(*) \n"
				. "FROM #__sermon_sermons AS j "
				. "LEFT JOIN #__sermon_speakers k ON j.speaker_id = k.id \n"
				.$this->seriesjoin
				. "WHERE j.published = '1'"
				. "AND YEAR(j.sermon_date)='".$this->year."' \n"
				.$this->and_m
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
		$orderby	= $this->_buildContentOrderBy();
		$database 	= &JFactory::getDBO();
		$query		= "SELECT *, k.id as s_id, ss.id as ss_id, ss.series_title \n"
					. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', j.id, j.alias) ELSE j.id END as slug \n"
					. "FROM #__sermon_sermons j \n"
					. "LEFT JOIN #__sermon_speakers k ON j.speaker_id = k.id \n"
					. "LEFT JOIN #__sermon_series AS ss ON j.series_id = ss.id \n"
					. "WHERE j.published='1' \n"
					. "AND YEAR(j.sermon_date)='".$this->year."' \n"
					.$this->and_m
					.$this->catwhere
					. "ORDER BY ".$orderby;
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

		return $rows;
	}
}