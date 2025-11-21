<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Latestsermons\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

/**
 * Helper class for Latest Sermons module
 *
 * @since  1.0
 */
class LatestsermonsHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Gets the items from the database
	 *
	 * @param \Joomla\Registry\Registry $params parameters
	 * @param CMSApplicationInterface   $app
	 *
	 * @return  array  $items  Array of items
	 *
	 * @throws \Exception
	 * @since  1.0
	 */
	public function getSermons(Registry $params, CMSApplicationInterface $app): array
	{
		$db     = $this->getDatabase();
		$user   = $app->getIdentity();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		$query = $db->getQuery(true);
		$query->select('a.title, a.id, a.sermon_date, a.audiofile, a.videofile, a.sermon_time, a.picture, a.notes, a.hits, a.catid, a.language');
		$query->select('b.title as speaker_title, b.pic, b.state AS speaker_state, b.catid AS speaker_catid, b.language AS speaker_language');
		$query->select('c.title AS series_title, c.avatar, c.state AS series_state, c.catid AS series_catid, c.language AS series_language');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END as speaker_slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as series_slug');
		$query->from('#__sermon_sermons AS a');
		$query->join('LEFT', '#__sermon_speakers AS b ON b.id = a.speaker_id');
		$query->join('LEFT', '#__sermon_series AS c ON c.id = a.series_id');
		$query->where('a.state = 1');

		if ($params->get('show_scripture'))
		{
			// Load component language file for bible books
			$lang = $app->getLanguage();
			$lang->load('com_sermonspeaker')
			|| $lang->load('com_sermonspeaker', JPATH_BASE . '/components/com_sermonspeaker');

			// Join over the scriptures
			$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text '
				. 'ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
			$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = a.id');
			$query->group('a.id');
		}

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// Category
		if ($cat = (int) $params->get('cat', 0))
		{
			// Subcategories
			if ($levels = (int) $params->get('show_subcategory_content', 0))
			{
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = ' . $cat);

				if ($levels > 0)
				{
					$subQuery->where('sub.level <= this.level + ' . $levels);
				}

				// Add the subquery to the main query
				$query->where('(a.catid = ' . $cat
					. ' OR a.catid IN (' . $subQuery->__toString() . '))');
			}
			else
			{
				$query->where('a.catid = ' . $cat);
			}
		}

		$query->select('cat.title AS category_title');
		$query->select('CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END as catslug');
		$query->join('LEFT', '#__categories AS cat ON cat.id = a.catid');
		$query->where('(a.catid = 0 OR (cat.access IN (' . $groups . ') AND cat.published = 1))');

		// Only sermons created by User
		if ($user->guest !== 1 && $params->get('created'))
		{
			$query->where('a.created_by = ' . $user->id);
		}

		// Others
		if ($speaker = (int) $params->get('speaker', 0))
		{
			$query->where('a.speaker_id = ' . $speaker);
		}

		if ($series = (int) $params->get('series', 0))
		{
			$query->where('a.series_id = ' . $series);
		}

		if ($idlist = $params->get('idlist', 0))
		{
			$id_array = explode(',', $idlist);
			$id_array = ArrayHelper::toInteger($id_array);
			$query->where('a.id IN (' . implode(',', $id_array) . ')');
		}

		// SmartFilter
		if ($params->get('smartfilter', 0))
		{
			$jinput = Factory::getApplication()->input;
			$view   = $jinput->getCmd('view');

			if ($view == 'speaker')
			{
				$query->where('a.speaker_id = ' . $jinput->getInt('id'));
			}
			elseif ($view == 'serie')
			{
				$query->where('a.series_id = ' . $jinput->getInt('id'));
			}
		}
		// Filetype filter
		if ($filetype = $params->get('filetype', 0))
		{
			if ($filetype == 2)
			{
				$query->where('a.videofile != ""');
			}
			else
			{
				$query->where('a.audiofile != ""');
			}
		}

		// Ordering
		$mode = $params->get('mode', 0);
		$dir  = $params->get('dir', 1) ? 'DESC' : 'ASC';

		switch ($mode)
		{
			case 0:
			default:
				$query->order('sermon_date ' . $dir . ', (sermon_number+0) ' . $dir);
				break;
			case 1:
				$query->order('a.hits ' . $dir . ', (sermon_number+0) ' . $dir);
				break;
			case 2:
				$query->order('a.created ' . $dir . ', (sermon_number+0) ' . $dir);
				break;
			case 3:
				$query->order('a.ordering ' . $dir . ', (sermon_number+0) ' . $dir);
				break;
			case 4:
				$query->order('a.title ' . $dir . ', (sermon_number+0) ' . $dir);
				break;
		}

		$db->setQuery($query, 0, (int) $params->get('ls_count', 3));

		return $db->loadObjectList();
	}
}
