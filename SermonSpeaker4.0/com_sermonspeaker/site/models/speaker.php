<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Sermon Model
 */
class SermonspeakerModelSpeaker extends JModel
{
	function __construct()
	{
		parent::__construct();
 
		$app = JFactory::getApplication();

		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
		$this->id		= JRequest::getInt('id',$this->params->get('speaker_id'));

		$this->limit = $app->getCfg('list_limit');
		// Get sorting order from Request and UserState
		$this->lists['order']		= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order",'filter_order','sermon_date','cmd' );
		$this->lists['order_Dir']	= $app->getUserStateFromRequest("com_sermonspeaker.sermons.filter_order_Dir",'filter_order_Dir','DESC','word' );
		// checking for invalid sorts from other views and change to default
		if ($this->lists['order'] == 'name'){ // columns speaker isn't shown in speaker views, would be obviously always the same
			$this->lists['order'] = 'sermon_date';
			$this->lists['order_Dir'] = 'DESC';
		}
	}

	function getOrder()
	{
        return $this->lists;
	}

	function _buildContentOrderBy() {
		return $this->lists['order'].' '.$this->lists['order_Dir'];
	}
	
	function getSeries()
	{
		$database = &JFactory::getDBO();
		$query = "SELECT `series`.`id`, `series_title`, `series_description`, `avatar` \n"
				."FROM `#__sermon_series` AS series \n"
				."JOIN `#__sermon_sermons` AS sermons ON sermons.series_id = series.id \n"
				."WHERE sermons.speaker_id = '".$this->id."' \n"
				."AND series.state = '1' \n"
				."AND sermons.state = '1' \n"
				."GROUP BY series.id \n"
				."ORDER BY series_title";

		$database->setQuery($query);
   		$series = $database->loadObjectList();
		
       return $series;
	}

	function getSermons()
	{
		$orderby	= $this->_buildContentOrderBy();
		$database	= &JFactory::getDBO();
		$query	= "SELECT sermons.id as sermons_id, sermon_number, sermon_scripture, sermon_title, sermon_time, sermon_path, notes, sermon_date, addfile, addfileDesc, series.id as series_id, series_title \n"
				. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', sermons.id, alias) ELSE sermons.id END as slug \n"
				. "FROM #__sermon_sermons AS sermons \n"
				. "LEFT JOIN #__sermon_series AS series ON sermons.series_id = series.id \n"
				. "WHERE sermons.speaker_id='".$this->id."' \n"
				. "AND sermons.state='1' \n"
				. "ORDER BY ".$orderby." \n";
		if ($this->params->get('limit_speaker') == 1) { 
			$query .= "LIMIT ".$this->limit;
		}
		$database->setQuery($query);
   		$sermons	= $database->loadObjectList();
		
       return $sermons;
	}
	
	function getData()
	{
		$database = &JFactory::getDBO();
		$query 	= "SELECT * \n"
				. "FROM #__sermon_speakers \n"
				. "WHERE id=".$this->id;
		$database->setQuery($query);
		$row = $database->loadObject();

		return $row;
	}
}