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
 
		$app = JFactory::getApplication();
		$this->limit = $app->getCfg('feed_limit');

		$this->params = $app->getParams();
	}

	function getData()
	{
		$cat['series'] 	= (int)$this->params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = (int)$this->params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));
		$cat['sermon'] 	= (int)$this->params->get('sermon_cat', JRequest::getInt('sermon_cat', ''));
		$series		 	= (int)$this->params->get('series_id', JRequest::getInt('series_id', ''));

		$this->catwhere = NULL;
		if ($cat['series'] != 0){
			$this->catwhere .= " AND series.catid = '".$cat['series']."' \n";
		}
		if ($cat['speaker'] != 0){
			$this->catwhere .= " AND speakers.catid = '".$cat['speaker']."' \n";
		}
		if ($cat['sermon'] != 0){
			$this->catwhere .= " AND sermons.catid = '".$cat['sermon']."' \n";
		}
		if ($series != 0){
			$this->catwhere .= " AND sermons.series_id = '".$series."' \n";
		}

		$database =& JFactory::getDBO();

		$query = "SET character_set_results ='utf8';";
		$database->setQuery($query);
		$query = "SELECT sermons.sermon_date, sermons.sermon_title, sermons.audiofile, sermons.videofile, series.series_title, \n"
				."sermons.notes, sermons.sermon_time, sermons.sermon_scripture, speakers.name, sermons.id \n"
				."FROM #__sermon_sermons AS sermons \n"
				."LEFT JOIN  #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id \n"
				."LEFT JOIN  #__sermon_series AS series ON sermons.series_id = series.id \n"
				."WHERE sermons.state='1' \n"
				."AND sermons.podcast='1' \n"
				.$this->catwhere
				."ORDER by sermons.sermon_date desc";
		$database->setQuery($query, '0', $this->limit);
		$rows = $database->loadObjectList();
		
		if(!count($rows)) echo mysql_error();

		return $rows;
	}
}