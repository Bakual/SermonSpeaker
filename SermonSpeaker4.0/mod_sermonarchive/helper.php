<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modSermonarchiveHelper
{
	public static function getList($params)
	{
		// Collect params
		$mode	= ($params->get('archive_switch') == 'month');

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('YEAR(`sermon_date`) AS `year`');
		$query->select("CONCAT_WS('-', YEAR(`sermon_date`), MONTH(`sermon_date`), '15') AS `date`");
		$query->from('#__sermon_sermons');
		$query->where('`state` = 1');
		$query->group('`year`');
		$query->order('`sermon_date` DESC');

		if ($mode){
			$query->select('MONTH(`sermon_date`) AS `month`');
			$query->group('`month`');
		} else {
			$query->select('0 AS `month`');
		}

		$db->setQuery($query, 0, (int)$params->get('archive_count'));
		$items	= $db->loadObjectList();

		return $items;
	}
}