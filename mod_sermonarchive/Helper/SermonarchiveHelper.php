<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonarchive\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

defined('_JEXEC') or die();

/**
 * Helper class for Sermonarchive module
 *
 * @since  1.0
 */
class SermonarchiveHelper implements DatabaseAwareInterface
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
	public function getSermons(object $params): array
	{
		// Collect params
		$mode  = ($params->get('archive_switch') == 'month');
		$state = (int) ($params->get('state', 1));
		$state = $state ?: 1;

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('YEAR(`sermon_date`) AS `year`');
		$query->select("CONCAT_WS('-', YEAR(`sermon_date`), MONTH(`sermon_date`), '15') AS `date`");
		$query->from('#__sermon_sermons');
		$query->where('`state` = ' . $state);

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

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

		return $db->loadObjectList();
	}
}
