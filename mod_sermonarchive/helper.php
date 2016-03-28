<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Helper class for SermonSpeaker module
 *
 * @since  1.0
 */
abstract class ModSermonarchiveHelper
{
	/**
	 * Gets the items from the database
	 *
	 * @param   object $params parameters
	 *
	 * @return  array  $items  Array of items
	 */
	public static function getList($params)
	{
		// Collect params
		$mode  = ($params->get('archive_switch') == 'month');
		$state = (int) ($params->get('state', 1));
		$state = $state ?: 1;

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('YEAR(`sermon_date`) AS `year`');
		$query->select("CONCAT_WS('-', YEAR(`sermon_date`), MONTH(`sermon_date`), '15') AS `date`");
		$query->from('#__sermon_sermons');
		$query->where('`state` = ' . $state);

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(`publish_up` = ' . $nullDate . ' OR `publish_up` <= ' . $nowDate . ')');
		$query->where('(`publish_down` = ' . $nullDate . ' OR `publish_down` >= ' . $nowDate . ')');

		$query->group('`year`');
		$query->order('`sermon_date` DESC');

		if ($params->get('sermon_cat'))
		{
			$query->where('catid = ' . (int) $params->get('sermon_cat'));
		}

		if ($mode)
		{
			$query->select('MONTH(`sermon_date`) AS `month`');
			$query->group('`month`');
		}
		else
		{
			$query->select('0 AS `month`');
		}

		$db->setQuery($query, 0, (int) $params->get('archive_count'));
		$items = $db->loadObjectList();

		return $items;
	}
}
