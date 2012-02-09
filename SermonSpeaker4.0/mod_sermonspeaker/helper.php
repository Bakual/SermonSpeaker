<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modSermonspeakerHelper
{
	public static function getList($params)
	{
		// Collect params
		$mode	= (int)$params->get('mode');
		$cat_id	= (int)$params->get('cat');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		if ($mode){
			$query->select('id, series_title as title, series_description as tooltip, avatar as pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug');
			$query->from('#__sermon_series');
			$query->order('series_title ASC'); // Add param here: ordering / name
		} else {
			$query->select('id, name as title, intro as tooltip, pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug');
			$query->from('#__sermon_speakers');
			$query->order('name ASC'); // Add param here: ordering / name
		}
		$query->where('state = 1');
		if ($cat_id){
			$query->where('catid = '.$cat_id);
		}
		$db->setQuery($query);
		$items	= $db->loadObjectList();

		return $items;
	}
}