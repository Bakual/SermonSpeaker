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
 
		global $mainframe, $option;
		
		$this->params = &JComponentHelper::getParams('com_sermonspeaker');
	}

	function getData()
	{
		$feedcat = $this->params->get('catid', JRequest::getInt('cat', ''));

		$database =& JFactory::getDBO();

		$seriesjoin = NULL;
		$catwhere = NULL;
		if ($feedcat != 0){
			$seriesjoin = "LEFT JOIN  #__sermon_series AS ss ON a.series_id = ss.id \n";
			$catwhere = "AND ss.catid = '".(int)$feedcat."' \n";
		}

		$query = "SET character_set_results ='utf8';";
		$database->setQuery($query);
		$query = "SELECT UNIX_TIMESTAMP(sermon_date) AS pubdate, sermon_title, sermon_path, \n"
				."notes, sermon_time, sermon_scripture, s.name, a.id \n"
				."FROM #__sermon_sermons AS a \n"
				."INNER JOIN  #__sermon_speakers AS s ON a.speaker_id = s.id \n"
				.$seriesjoin
				."WHERE a.published='1' \n"
				."AND a.podcast='1' \n"
				.$catwhere
				."ORDER by pubdate desc";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		if(!count($rows)) echo mysql_error();

		return $rows;
	}
}