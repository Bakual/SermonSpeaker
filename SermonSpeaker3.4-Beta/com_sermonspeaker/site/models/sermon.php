<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Sermon Model
 */
class SermonspeakerModelSermon extends JModel
{
	function __construct()
	{
		parent::__construct();
 
		global $mainframe, $option;
		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$this->id	= JRequest::getInt('id',$params->get('sermon_id'));
	}

	function getSerie($serie_id)
	{
		$database = &JFactory::getDBO();
		$query	= "SELECT series_title \n"
				. "FROM #__sermon_series \n"
				. "WHERE id=".$serie_id;
		$database->setQuery($query);
		$seriesname = $database->loadResult();
		
       return $seriesname;
	}
	
	function getSpeaker($speaker_id)
	{
		$database = &JFactory::getDBO();
      	$query	= "SELECT name,pic \n"
				. "FROM #__sermon_speakers \n"
				. "WHERE id=".$speaker_id;
		$database->setQuery($query);
      	$rows = $database->loadObjectList();
      	$speaker = $rows['0']; // todo: andere Abfrageform wählen, scheint mir etwas umständlich
		
       return $speaker;
	}

	function getData()
	{
		$database = &JFactory::getDBO();
		$query	= "SELECT * \n"
				. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. "FROM #__sermon_sermons \n"
				. "WHERE id=".$this->id;
		$database->setQuery($query);
		$row	= $database->loadObjectList();
		$row[0]->sermon_path = rtrim($row[0]->sermon_path);
		return $row;
	}
}