<?php
defined('_JEXEC') or die;

/**
 * SermonSpeaker Component Feed Model
 */
class SermonspeakerModelFeed extends JModelLegacy
{
	function getData()
	{
		$app	= JFactory::getApplication();
		$params	= $app->getParams();
		$jinput	= $app->input;
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$db		= $this->getDbo();

		// Force utf8 connection
		$query = "SET character_set_results ='utf8';";
		$db->setQuery($query);

		// Create a new query object.
		$query	= $db->getQuery(true);

		// Select required fields from the table.
		$query->select('sermons.sermon_date, sermons.sermon_title, sermons.audiofile, sermons.videofile, sermons.notes, sermons.sermon_time, sermons.id, sermons.picture');
		$query->select('CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug');
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the scriptures.
		$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
		$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = sermons.id');
		$query->group('sermons.id');

		// Join over Speaker
		$query->select('speakers.name AS name, speakers.pic AS pic');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over Series
		$query->select('series.series_title AS series_title, series.avatar');
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Join over Sermons Category.
		$query->join('LEFT', '#__categories AS c_sermons ON c_sermons.id = sermons.catid');
		$query->where('(sermons.catid = 0 OR (c_sermons.access IN ('.$groups.') AND c_sermons.published = 1))');

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->where('(sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN ('.$groups.') AND c_speaker.published = 1))');

		// Join over Series Category.
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('(sermons.series_id = 0 OR series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');

		// Category filter
		if ($id	= $jinput->get('catid', $params->get('catid', 0), 'int'))
		{
			$type	= $params->get('count_items_type', 'sermons');
			$query->where($type.'.catid = '.$id);
		}

		// Filter by type
		$feedtype	= $jinput->get('type', 'auto');
		if ($feedtype == 'video')
		{
			$query->where('sermons.videofile != ""');
		}
		elseif ($feedtype == 'audio')
		{
			$query->where('sermons.audiofile != ""');
		}

		// Filter by state
		$query->where('sermons.podcast = 1');
		$query->where('sermons.state != -2');

		// Grouping
		$query->order('sermons.sermon_date DESC');

		$db->setQuery($query, '0', $app->getCfg('feed_limit'));
		$rows = $db->loadObjectList();

		return $rows;
	}
}