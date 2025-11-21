<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Search
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Plugin\Search\Sermonspeaker\Extension;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper;

defined('_JEXEC') or die();

/**
 * SermonSpeaker Search plugin
 *
 * @since  1.0
 */
final class Sermonspeaker extends CMSPlugin implements SubscriberInterface
{
	use DatabaseAwareTrait;

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  6.0.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   5.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onContentSearchAreas' => 'onContentSearchAreas',
			'onContentSearch'      => 'onContentSearch',
		];
	}

	/**
	 * Defines the search areas
	 *
	 * @return  array  An array of search areas
	 * @since 1.0.0
	 */
	public function onContentSearchAreas(): array
	{
		static $areas = array();
		$areas['spsermons'] = 'PLG_SEARCH_SERMONSPEAKER_SERMONS';

		if ($this->params->get('search_series', 1))
		{
			$areas['spseries'] = 'PLG_SEARCH_SERMONSPEAKER_SERIES';
		}

		if ($this->params->get('search_speakers', 1))
		{
			$areas['spspeakers'] = 'PLG_SEARCH_SERMONSPEAKER_SPEAKERS';
		}

		if ($this->params->get('sermons_speaker', 0))
		{
			$db    = $this->getDatabase();
			$query = "SELECT `id`, `title` FROM #__sermon_speakers WHERE state = '1'";
			$db->setQuery($query);
			$speakers = $db->loadAssocList();

			foreach ($speakers as $speaker)
			{
				$areas['spspeakers-' . $speaker['id']] = Text::sprintf('PLG_SEARCH_SERMONSPEAKER_SERMONS_FROM', $speaker['title']);
			}
		}

		return $areas;
	}

	/**
	 * SermonSpeaker Search method
	 *
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 *
	 * @param   object  $event
	 *
	 * @return  void
	 * @since 1.0
	 */
	public function onContentSearch(object $event): void
	{
		// Using numbers since com_search doesn't use new plugin events
		$text     = $event->getArgument(0);
		$phrase   = $event->getArgument(1);
		$ordering = $event->getArgument(2);
		$areas    = $event->getArgument(3);

		$db     = $this->getDatabase();
		$app    = $this->getApplication();
		$user   = $app->getIdentity();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query  = 'SELECT `book` FROM #__sermon_scriptures ORDER BY `book` DESC LIMIT 1';
		$db->setQuery($query);
		$max   = $db->loadResult();
		$books = array();

		for ($i = 1; $i <= $max; $i++)
		{
			$books[$i] = strtolower(Text::_('COM_SERMONSPEAKER_BOOK_' . $i));
		}

		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return;
			}
		}
		else
		{
			$areas = array_keys($this->onContentSearchAreas());
		}

		$sContent  = $this->params->get('search_content', 1);
		$sArchived = $this->params->get('search_archived', 1);
		$limit     = $this->params->def('search_limit', 50);
		$state     = array();

		if ($sContent)
		{
			$state[] = 1;
		}

		if ($sArchived)
		{
			$state[] = 2;
		}

		$text = trim($text);

		if ($text == '')
		{
			return;
		}

		$speakers = array();

		if ($this->params->get('sermons_speaker', 0))
		{
			foreach ($areas as $area)
			{
				if (str_starts_with($area, 'spspeakers-'))
				{
					$speakers[] = (int) substr($area, 11);
				}
			}
		}

		$rows = array();

		if (in_array('spsermons', $areas) || $speakers)
		{
			$section = Text::_('PLG_SEARCH_SERMONSPEAKER_SERMONS');
			$wheres  = array();

			switch ($phrase)
			{
				case 'exact':
					$wheres2  = array();
					$book_ids = array();

					foreach ($books as $key => $value)
					{
						if (str_contains($value, $text))
						{
							$book_ids[] = $key;
						}
					}

					if ($book_ids)
					{
						$wheres2[] = 'b.book IN (' . implode(',', $book_ids) . ')';
					}

					$text      = $db->quote('%' . $db->escape($text, true) . '%', false);
					$wheres2[] = 'a.title LIKE ' . $text;
					$wheres2[] = 'a.notes LIKE ' . $text;
					$where     = '(' . implode(') OR (', $wheres2) . ')';
					break;
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $text);

					foreach ($words as $word)
					{
						$wheres2  = array();
						$book_ids = array();

						foreach ($books as $key => $value)
						{
							if (str_contains($value, $text))
							{
								$book_ids[] = $key;
							}
						}

						if ($book_ids)
						{
							$wheres2[] = 'b.book IN (' . implode(',', $book_ids) . ')';
						}

						$word      = $db->quote('%' . $db->escape($word, true) . '%', false);
						$wheres2[] = 'a.title LIKE ' . $word;
						$wheres2[] = 'a.notes LIKE ' . $word;
						$wheres[]  = implode(' OR ', $wheres2);
					}

					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;
				case 'popular':
					$order = 'a.hits DESC';
					break;
				case 'alpha':
					$order = 'a.title ASC';
					break;
				case 'category':
					$order  = 'c.title ASC, a.title ASC';
					$morder = 'a.title ASC';
					break;
				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state))
			{
				$query = $db->getQuery(true);
				$query->select('a.title, a.notes AS text, a.created AS created, a.catid, a.language, '
					. 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
					. 'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
					. 'CONCAT_WS(" / ", ' . $db->quote($section) . ', c.title) AS section, "2" AS browsernav');
				$query->from('#__sermon_sermons AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->leftJoin('#__sermon_scriptures AS b ON b.sermon_id = a.id');
				$query->group('a.id');
				$query->where('(' . $where . ')');
				$query->where('a.state in (' . implode(',', $state) . ')');

				if (!in_array('spsermons', $areas))
				{
					$query->where('a.speaker_id in (' . implode(',', $speakers) . ')');
				}

				$query->where('(c.published = 1 AND c.access IN (' . $groups . '))');
				$query->order($order);

				// Filter by language
				if ($app->isClient('site') && $app->getLanguageFilter())
				{
					$tag = $this->app->getLanguage()->getTag();
					$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if ($list)
				{
					foreach ($list as $item)
					{
						$item->href = RouteHelper::getSermonRoute($item->slug, $item->catid, $item->language);
					}
				}

				$rows = array_merge($list, $rows);
			}
		}

		if (in_array('spseries', $areas))
		{
			$section = Text::_('PLG_SEARCH_SERMONSPEAKER_SERIES');
			$wheres  = array();

			switch ($phrase)
			{
				case 'exact':
					$text      = $db->quote('%' . $db->escape($text, true) . '%', false);
					$wheres2   = array();
					$wheres2[] = 'a.title LIKE ' . $text;
					$wheres2[] = 'a.series_description LIKE ' . $text;
					$where     = '(' . implode(') OR (', $wheres2) . ')';
					break;
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $text);

					foreach ($words as $word)
					{
						$word      = $db->quote('%' . $db->escape($word, true) . '%', false);
						$wheres2   = array();
						$wheres2[] = 'a.title LIKE ' . $word;
						$wheres2[] = 'a.series_description LIKE ' . $word;
						$wheres[]  = implode(' OR ', $wheres2);
					}

					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;
				case 'popular':
					$order = 'a.hits DESC';
					break;
				case 'alpha':
					$order = 'a.title ASC';
					break;
				case 'category':
					$order  = 'c.title ASC, a.title ASC';
					$morder = 'a.title ASC';
					break;
				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state))
			{
				$query = $db->getQuery(true);
				$query->select('a.title, a.series_description AS text, a.created AS created, a.catid, a.language, '
					. 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
					. 'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
					. 'CONCAT_WS(" / ", ' . $db->quote($section) . ', c.title) AS section, "2" AS browsernav');
				$query->from('#__sermon_series AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('(' . $where . ')');
				$query->where('a.state in (' . implode(',', $state) . ')');
				$query->where('(c.published = 1 AND c.access IN (' . $groups . '))');
				$query->order($order);

				// Filter by language
				if ($app->isClient('site') && $app->getLanguageFilter())
				{
					$tag = $this->app->getLanguage()->getTag();
					$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if ($list)
				{
					foreach ($list as $item)
					{
						$item->href = RouteHelper::getSerieRoute($item->slug, $item->catid, $item->language);
					}
				}

				$rows = array_merge($list, $rows);
			}
		}

		if (in_array('spspeakers', $areas))
		{
			$section = Text::_('PLG_SEARCH_SERMONSPEAKER_SPEAKERS');
			$wheres  = array();

			switch ($phrase)
			{
				case 'exact':
					$text      = $db->quote('%' . $db->escape($text, true) . '%', false);
					$wheres2   = array();
					$wheres2[] = 'a.title LIKE ' . $text;
					$wheres2[] = 'a.intro LIKE ' . $text;
					$wheres2[] = 'a.bio LIKE ' . $text;
					$where     = '(' . implode(') OR (', $wheres2) . ')';
					break;
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $text);

					foreach ($words as $word)
					{
						$word      = $db->quote('%' . $db->escape($word, true) . '%', false);
						$wheres2   = array();
						$wheres2[] = 'a.title LIKE ' . $word;
						$wheres2[] = 'a.intro LIKE ' . $word;
						$wheres2[] = 'a.bio LIKE ' . $word;
						$wheres[]  = implode(' OR ', $wheres2);
					}

					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;
				case 'popular':
					$order = 'a.hits DESC';
					break;
				case 'alpha':
					$order = 'a.title ASC';
					break;
				case 'category':
					$order  = 'c.title ASC, a.title ASC';
					$morder = 'a.title ASC';
					break;
				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state))
			{
				$query = $db->getQuery(true);
				$query->select('a.title, CONCAT_WS(" ", a.intro, a.bio) AS text, a.created AS created, a.catid, a.language, '
					. 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
					. 'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
					. 'CONCAT_WS(" / ", ' . $db->quote($section) . ', c.title) AS section, "2" AS browsernav');
				$query->from('#__sermon_speakers AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('(' . $where . ')');
				$query->where('a.state in (' . implode(',', $state) . ')');
				$query->where('(c.published = 1 AND c.access IN (' . $groups . '))');
				$query->order($order);

				// Filter by language
				if ($app->isClient('site') && $app->getLanguageFilter())
				{
					$tag = $this->app->getLanguage()->getTag();
					$query->where('c.language in (' . $db->quote($tag) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if ($list)
				{
					foreach ($list as $item)
					{
						$item->href = RouteHelper::getSpeakerRoute($item->slug, $item->catid, $item->language);
					}
				}

				$result   = $event->getArgument('result', []);
				$result[] = array_merge($list, $rows);
				$event->setArgument('result', $result);
			}
		}
	}
}
