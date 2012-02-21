<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modLatestsermonsHelper
{
	public static function getList($params)
	{
		// Collect params
		$cat_id	= (int)$params->get('cat');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.sermon_title, a.id, a.sermon_date, b.name, c.series_title, a.audiofile, a.videofile, a.sermon_time, a.picture, b.pic');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug');
		$query->from('#__sermon_sermons AS a');
		$query->join('LEFT', '#__sermon_speakers AS b ON b.id = a.speaker_id');
		$query->join('LEFT', '#__sermon_series AS c ON c.id = a.series_id');
		$query->where('a.state = 1');
		$query->order('sermon_date DESC, (sermon_number+0) DESC');
		if ($params->get('sermon_cat')){
			$query->where('a.catid = '.(int)$params->get('sermon_cat'));
		}
		if ($params->get('speaker_cat')){
			$query->where('b.catid = '.(int)$params->get('speaker_cat'));
		}
		if ($params->get('series_cat')){
			$query->where('c.catid = '.(int)$params->get('series_cat'));
		}

		$db->setQuery($query, 0, (int)$params->get('ls_count'));
		$items	= $db->loadObjectList();

		return $items;
	}
}