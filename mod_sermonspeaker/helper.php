<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Helper class for SermonSpeaker module
 *
 * @since  1.0
 */
abstract class ModSermonspeakerHelper
{
	/**
	 * Gets the items from the database
	 *
	 * @param   object  $params  parameters
	 *
	 * @return  array  $items  Array of items
	 *
	 * @since ?
	 */
	public static function getList($params)
	{
		// Collect params
		$mode   = (int) $params->get('mode');
		$cat_id = ($mode) ? (int) $params->get('catseries') : (int) $params->get('catspeakers');
		$sort   = (int) $params->get('sort');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		if ($mode)
		{
			$query->select('id, title, catid, language, series_description as tooltip, avatar as pic');
			$query->select('CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
			$query->from('#__sermon_series');

			if ($sort)
			{
				$query->order('ordering ASC');
			}
			else
			{
				$query->order('title ASC');
			}
		}
		else
		{
			$query->select('id, title, catid, language, intro as tooltip, pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
			$query->from('#__sermon_speakers');

			if ($sort)
			{
				$query->order('ordering ASC');
			}
			else
			{
				$query->order('title ASC');
			}
		}

		$query->where('state = 1');

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')');
		$query->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');

		if ($cat_id)
		{
			$query->where('catid = ' . $cat_id);
		}

		$db->setQuery($query, 0, (int) $params->get('limit', 0));
		$items = $db->loadObjectList();

		return $items;
	}
}
