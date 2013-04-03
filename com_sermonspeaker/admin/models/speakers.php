<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class SermonspeakerModelSpeakers extends JModel
{
	function __construct()
	{
		parent::__construct();

		$app 		= JFactory::getApplication();
		$this->db	=& JFactory::getDBO();

		$this->filter_state		= $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_state",'filter_state','','word');
		$this->filter_catid		= $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_catid",'filter_catid','','int');
		$this->search			= $app->getUserStateFromRequest("com_sermonspeaker.speakers.search",'search','','string');
		$this->search			= JString::strtolower($this->search);

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getInt('limitstart', 0);
 		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get sorting order from Request and UserState
		$this->_order['order']		= $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_order",'filter_order','id','cmd' );
		$this->_order['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_order_Dir",'filter_order_Dir','DESC','word' );
	}

	function _buildWhere()
	{
		$where = NULL;
		if ($this->filter_state) {
			if ($this->filter_state == 'P') {
				$where[] = 'speakers.published = 1';
			}
			else if ($this->filter_state == 'U') {
				$where[] = 'speakers.published = 0';
			}
		}
		if ($this->filter_catid) {
			$where[] = 'speakers.catid = ' . (int) $this->filter_catid;
		}
		if ($this->search) {
			$where[] = 'LOWER(speakers.name) LIKE '.$this->db->Quote('%'.$this->db->getEscaped($this->search, true).'%', false);
		}
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		return $where;
	}
	
	function getTotal()
	{
		$where	= $this->_buildWhere();
		// Query bilden
		$query = 'SELECT *'
		.' FROM #__sermon_speakers AS speakers'
		.$where
		;
		
		// Query ausführen und Einträge zählen (einzeiliges Resultat als Integer)
		$total = $this->_getListCount($query);    

        return $total;
	}

	function getSpeakers()
	{
		$where	= $this->_buildWhere();
		$orderby 	= ' ORDER BY '.$this->_order['order'].' '.$this->_order['order_Dir'];
		// Query bilden
        $query = "SELECT speakers.*, cc.title \n"
				."FROM #__sermon_speakers AS speakers \n"
				."LEFT JOIN #__categories AS cc ON cc.id = speakers.catid \n"
				.$where
				.$orderby;
		// Query ausführen (mehrzeiliges Resulat als Array)
		$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

        return $rows;
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