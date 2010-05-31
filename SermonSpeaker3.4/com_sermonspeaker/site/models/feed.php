<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Series Model
 */
class SermonspeakerModelFeed extends JModel
{
	function __construct()
	{
		parent::__construct();
 
		global $option;
		
		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
	}

	function getData()
	{
		$cat['series'] = $this->params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = $this->params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));
		$cat['sermon'] = $this->params->get('sermon_cat', JRequest::getInt('sermon_cat', ''));

		$this->seriesjoin = NULL;
		$this->catwhere = NULL;
		if ($cat['series'] != 0){
			$this->seriesjoin = " LEFT JOIN #__sermon_series AS ss ON a.series_id = ss.id \n";
			$this->catwhere .= " AND ss.catid = '".(int)$cat['series']."' \n";
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND s.catid = '".(int)$cat['speaker']."' \n";
		}
		if ($cat['sermon'] != 0){
			$this->catwhere .= " AND a.catid = '".(int)$cat['sermon']."' \n";
		}

		$database =& JFactory::getDBO();

		$query = "SET character_set_results ='utf8';";
		$database->setQuery($query);
		$query = "SELECT UNIX_TIMESTAMP(sermon_date) AS pubdate, sermon_title, sermon_path, \n"
				."notes, sermon_time, sermon_scripture, s.name, a.id \n"
				."FROM #__sermon_sermons AS a \n"
				."LEFT JOIN  #__sermon_speakers AS s ON a.speaker_id = s.id \n"
				.$this->seriesjoin
				."WHERE a.published='1' \n"
				."AND a.podcast='1' \n"
				.$this->catwhere
				."ORDER by pubdate desc";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		if(!count($rows)) echo mysql_error();

		return $rows;
	}
}