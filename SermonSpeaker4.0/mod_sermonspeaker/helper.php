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
		if ($mode == 2){
			$categories = JCategories::getInstance('Sermonspeaker');
			$root	= $categories->get($cat_id);
			if ($root->hasChildren()){
				$categories = $root->getChildren(true);
				$items = array();
				foreach ($categories as $category){
					if($category->published != 1){
						continue;
					}
					$item	= new stdclass;
					$params = $category->getParams();
					$item->id		= $category->id;
					$item->slug		= $category->slug;
					$item->title	= $category->title;
					$item->tooltip	= $category->description;
					$item->level	= $category->level;
					$item->pic		= $params->get('image');
					$items[]	= $item;
				}
			} else {
				// No Children found -> nothing to show.
				return;
			}
		} else {
			if ($mode){
				$query->select('id, series_title as title, series_description as tooltip, avatar as pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
				$query->from('#__sermon_series');
				$query->order('series_title ASC'); // Add param here: ordering / name
			} else {
				$query->select('id, name as title, intro as tooltip, pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
				$query->from('#__sermon_speakers');
				$query->order('name ASC'); // Add param here: ordering / name
			}
			$query->where('state = 1');
			if ($cat_id){
				$query->where('catid = '.$cat_id);
			}
			$db->setQuery($query);
			$items	= $db->loadObjectList();
		}

		return $items;
	}
}