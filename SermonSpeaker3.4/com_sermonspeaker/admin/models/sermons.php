<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSermons extends JModel
{
	function __construct()
	{
		parent::__construct();

		$app 		= JFactory::getApplication();
		$this->db	=& JFactory::getDBO();

		$this->filter_state		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_state",'filter_state','','word');
		$this->filter_catid		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_catid",'filter_catid','','int');
		$this->filter_pcast		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_pcast",'filter_pcast','','word');
		$this->filter_serie		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_serie",'filter_serie','','string');
		$this->search			= $app->getUserStateFromRequest("com_sermonspeaker.sermons.search",'search','','string');
		$this->search			= JString::strtolower($this->search);

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get sorting order from Request and UserState
		$this->_order['order']		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order",'filter_order','id','cmd' );
		$this->_order['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order_Dir",'filter_order_Dir','DESC','word' );
	}

	function _buildWhere()
	{
		$where = NULL;
		if ($this->filter_state) {
			if ($this->filter_state == 'P') {
				$where[] = 'sermons.published = 1';
			}
			else if ($this->filter_state == 'U') {
				$where[] = 'sermons.published = 0';
			}
		}
		if ($this->filter_catid) {
			$where[] = 'cc.id = ' . (int) $this->filter_catid;
		}
		if ($this->filter_serie) {
			$where[] = 'sermons.series_id = "' . $this->filter_serie . '"';
		}
		if ($this->filter_pcast) {
			if ($this->filter_pcast == 'P') {
				$where[] = 'sermons.podcast = 1';
			}
			else if ($this->filter_pcast == 'U') {
				$where[] = 'sermons.podcast = 0';
			}
		}
		if ($this->search) {
			$where[] = 'LOWER(sermons.sermon_title) LIKE '.$this->db->Quote('%'.$this->db->getEscaped($this->search, true).'%', false);
		}
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}
	
	function getTotal()
	{
		$where	= $this->_buildWhere();
		// Query bilden
		$query = 'SELECT sermons.*'
		.' FROM #__sermon_sermons AS sermons'
		.$where
		;
		
		// Query ausführen und Einträge zählen (einzeiliges Resultat als Integer)
		$total = $this->_getListCount($query);    

        return $total;
	}

	function getSermons()
	{
		$where	= $this->_buildWhere();
		$orderby 	= ' ORDER BY '.$this->_order['order'].' '.$this->_order['order_Dir'];
		// Query bilden
        $query = "SELECT sermons.*, speaker.name, series.series_title, cc.title \n"
				."FROM #__sermon_sermons AS sermons \n"
				."LEFT JOIN #__sermon_speakers AS speaker ON sermons.speaker_id = speaker.id \n"
				."LEFT JOIN #__sermon_series AS series ON sermons.series_id = series.id \n"
				."LEFT JOIN #__categories AS cc ON cc.id = sermons.catid \n"
				.$where
				.$orderby;
		// Query ausführen (mehrzeiliges Resulat als Array)
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

        return $rows;
	}

	function getSerieList()
	{
		$query = 'SELECT series_title, id '
		. ' FROM #__sermon_series'
		. ' ORDER BY ordering ASC'
		;
		
		// Query ausführen (mehrzeiliges Resulat als Array)
		$series	= $this->_getList($query);

        return $series;
	}

	function getPagination()
	{
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}
}