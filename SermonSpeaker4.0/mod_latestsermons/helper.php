<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modLatestsermonsHelper
{
	public static function getList($params)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.sermon_title, a.id, a.sermon_date, a.audiofile, a.videofile, a.sermon_time, a.picture, a.notes, a.hits');
		$query->select('b.name, b.pic, b.state AS speaker_state, c.series_title, c.state AS series_state');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END as speaker_slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as series_slug');
		$query->from('#__sermon_sermons AS a');
		$query->join('LEFT', '#__sermon_speakers AS b ON b.id = a.speaker_id');
		$query->join('LEFT', '#__sermon_series AS c ON c.id = a.series_id');
		$query->where('a.state = 1');
		if ($params->get('mode', 0))
		{
			$query->order('a.hits DESC, (sermon_number+0) DESC');
		}
		else
		{
			$query->order('sermon_date DESC, (sermon_number+0) DESC');
		}
		if ($cat = (int)$params->get('cat', 0))
		{
			switch ($params->get('cat_type'))
			{
				case 'sermons':
					$query->where('a.catid = '.$cat);
					break;
				case 'speakers':
					$query->where('b.catid = '.$cat);
					break;
				case 'series':
					$query->where('c.catid = '.$cat);
					break;
			}
		}
		if ($speaker = (int)$params->get('speaker', 0))
		{
			$query->where('a.speaker_id = '.$speaker);
		}
		if ($series = (int)$params->get('series', 0))
		{
			$query->where('a.series_id = '.$series);
		}
		if ($idlist = $params->get('idlist', 0))
		{
			$id_array = explode(',', $idlist);
			JArrayHelper::toInteger($id_array);
			$query->where('a.id IN ('.implode(',', $id_array).')');
		}

		$db->setQuery($query, 0, (int)$params->get('ls_count', 3));
		$items	= $db->loadObjectList();

		return $items;
	}
}