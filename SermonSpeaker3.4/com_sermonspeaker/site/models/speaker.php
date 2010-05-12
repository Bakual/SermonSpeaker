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
 
		global $mainframe, $option;
		
		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
		$this->id		= JRequest::getInt('id',$this->params->get('speaker_id'));
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
		$database	= &JFactory::getDBO();
		if ($this->params->get('limit_speaker') == 1) { 
			$query	= "SELECT id, sermon_number,sermon_scripture, sermon_title, sermon_time, notes,sermon_date, addfile, addfileDesc \n"
					. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
					. "FROM #__sermon_sermons \n"
					. "WHERE  speaker_id='".$this->id."' \n"
					. "AND published='1' \n"
					. "ORDER BY sermon_date DESC, (sermon_number+0) DESC \n"
					. "LIMIT ".$this->params->get('sermonresults');
		} else {
			$query = "SELECT id, sermon_number,sermon_scripture, sermon_title, sermon_time, notes,sermon_date, addfile, addfileDesc \n"
					. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
					. "FROM #__sermon_sermons \n"
					. "WHERE  speaker_id='".$this->id."' \n"
					. "AND published='1' \n"
					. "ORDER BY sermon_date DESC, (sermon_number+0) DESC";
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