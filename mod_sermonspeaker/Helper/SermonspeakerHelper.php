<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonspeaker\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

defined('_JEXEC') or die();

/**
 * Helper class for SermonSpeaker module
 *
 * @since  1.0
 */
class SermonspeakerHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Gets the items from the database
	 *
	 * @param object $params parameters
	 *
	 * @return  array  $items  Array of items
	 *
	 * @since ?
	 */
	public function getItems(object $params): array
	{
		// Collect params
		$mode   = (int) $params->get('mode');
		$cat_id = ($mode) ? (int) $params->get('catseries') : (int) $params->get('catspeakers');
		$sort   = (int) $params->get('sort');

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		if ($mode)
		{
			$query->select('id, title, catid, language, series_description as tooltip, avatar as pic');
			$query->select('CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
			$query->from('#__sermon_series');

		}
		else
		{
			$query->select('id, title, catid, language, intro as tooltip, pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
			$query->from('#__sermon_speakers');

		}

		if ($sort)
		{
			$query->order('ordering ASC');
		}
		else
		{
			$query->order('title ASC');
		}

		$query->where('state = 1');

		// Filter by start and end dates.
		$nowDate = Factory::getDate()->toSql();

		$query->where('(publish_up IS NULL OR publish_up <= :publishUp)');
		$query->where('(publish_down IS NULL OR publish_down >= :publishDown)');
		$query->bind(':publishUp', $nowDate);
		$query->bind(':publishDown', $nowDate);

		if ($cat_id)
		{
			$query->where('catid = ' . $cat_id);
		}

		$db->setQuery($query, 0, (int) $params->get('limit', 0));

		return $db->loadObjectList();
	}
}
