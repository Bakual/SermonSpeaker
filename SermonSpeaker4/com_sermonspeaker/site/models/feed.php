<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * SermonSpeaker Component Feed Model
 */
class SermonspeakerModelFeed extends JModel
{
	function __construct()
	{
		parent::__construct();
 
		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
	}

	function getData()
	{
		$cat['series'] = $this->params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = $this->params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));
		$cat['sermon'] = $this->params->get('sermon_cat', JRequest::getInt('sermon_cat', ''));

		$this->catwhere = NULL;
		if ($cat['series'] != 0){
			$this->catwhere .= " AND series.catid = '".(int)$cat['series']."' \n";
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND speakers.catid = '".(int)$cat['speaker']."' \n";
		}
		if ($cat['sermon'] != 0){
			$this->catwhere .= " AND sermons.catid = '".(int)$cat['sermon']."' \n";
		}

		$database =& JFactory::getDBO();

		$query = "SET character_set_results ='utf8';";
		$database->setQuery($query);
		$query = "SELECT sermons.sermon_date, sermons.sermon_title, sermons.sermon_path, series.series_title, \n"
				."sermons.notes, sermons.sermon_time, sermons.sermon_scripture, speakers.name, sermons.id \n"
				."FROM #__sermon_sermons AS sermons \n"
				."LEFT JOIN  #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id \n"
				."LEFT JOIN  #__sermon_series AS series ON sermons.series_id = series.id \n"
				."WHERE sermons.published='1' \n"
				."AND sermons.podcast='1' \n"
				.$this->catwhere
				."ORDER by sermons.sermon_date desc";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		if(!count($rows)) echo mysql_error();

		return $rows;
	}
}