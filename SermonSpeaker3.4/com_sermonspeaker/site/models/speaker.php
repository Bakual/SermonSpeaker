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
        $query	= "SELECT j.id, speaker_id, series_title, series_description, "
            . " published, ordering, hits, created_by, created_on, j.avatar "
            . " FROM #__sermon_series j \n"
            . " WHERE (speaker_id='".$this->id."' OR speaker2='".$this->id."' OR speaker3='".$this->id
			. "' OR speaker4='".$this->id."' OR speaker5='".$this->id."' OR speaker6='".$this->id
			. "' OR speaker7='".$this->id."' OR speaker8='".$this->id."' OR speaker9='".$this->id
			. "' OR speaker10='".$this->id."' OR speaker11='".$this->id."' OR speaker12='".$this->id
			. "' OR speaker13='".$this->id."' OR speaker14='".$this->id."' OR speaker15='".$this->id
			. "' OR speaker16='".$this->id."' OR speaker17='".$this->id."' OR speaker18='".$this->id
			. "' OR speaker19='".$this->id."' OR speaker20='".$this->id."') \n"
            . " AND published = '1' \n"
			. " ORDER BY series_title";
  
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
				. "AND sermons.published='1' \n"
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