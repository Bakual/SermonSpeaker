<?php
defined('_JEXEC') or die;

/**
 * SermonSpeaker Component Feed Model
 */
class SermonspeakerModelFeed extends JModelLegacy
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
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$jinput	= JFactory::getApplication()->input;

		// Category filter (priority on request so subcategories work)
		if ($id	= $jinput->get('sermon_cat', 0, 'int'))
		{
			$type	= 'sermons';
		}
		elseif ($id	= $jinput->get('speaker_cat', 0, 'int'))
		{
			$type	= 'speakers';
		}
		elseif ($id	= $jinput->get('series_cat', 0, 'int'))
		{
			$type	= 'series';
		}
		else
		{
			$id		= (int) $this->params->get('catid', 0);
			$type	= $this->params->get('count_items_type', 'sermons');
		}
//		$this->setState('category.id', $id);
//		$this->setState('category.type', $type);
		$this->catwhere = ($id) ? " AND ".$type.".catid = ".$id." \n" : '';

		$database = JFactory::getDBO();

		$query = "SET character_set_results ='utf8';";
		$database->setQuery($query);
		$query = "SELECT sermons.sermon_date, sermons.sermon_title, sermons.audiofile, sermons.videofile, series.series_title, series.avatar, \n"
				."sermons.notes, sermons.sermon_time, speakers.name, speakers.pic, sermons.id, sermons.picture, \n"
				.'GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text ORDER BY script.ordering ASC SEPARATOR "!") AS scripture '."\n"
				."FROM #__sermon_sermons AS sermons \n"
				."LEFT JOIN #__sermon_scriptures AS script ON script.sermon_id = sermons.id \n"
				."LEFT JOIN #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id \n"
				."LEFT JOIN #__sermon_series AS series ON sermons.series_id = series.id \n"
				."LEFT JOIN #__categories AS c_sermons ON c_sermons.id = sermons.catid \n"
				."LEFT JOIN #__categories AS c_speaker ON c_speaker.id = speakers.catid \n"
				."LEFT JOIN #__categories AS c_series ON c_series.id = series.catid \n"
				."WHERE sermons.podcast='1' \n"
				."AND sermons.state != '-2' \n"
				."AND (sermons.catid = 0 OR (c_sermons.access IN (".$groups.") AND c_sermons.published = 1)) \n"
				."AND (sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN (".$groups.") AND c_speaker.published = 1)) \n"
				."AND (sermons.series_id = 0 OR series.catid = 0 OR (c_series.access IN (".$groups.") AND c_series.published = 1)) \n"
				.$this->catwhere
				."GROUP BY sermons.id \n"
				."ORDER BY sermons.sermon_date DESC";

		$database->setQuery($query, '0', $this->limit);
		$rows = $database->loadObjectList();

		return $rows;
	}
}