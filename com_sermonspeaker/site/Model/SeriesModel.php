<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Sermonspeaker\Component\Sermonspeaker\Site\Trait\CategoryTreeTrait;

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SeriesModel extends ListModel
{
	use CategoryTreeTrait;

	/**
	 * Constructor
	 *
	 * @param array $config An optional associative array of configuration settings
	 *
	 * @since ?
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'series.title',
				'ordering', 'series.ordering',
				'series_description', 'series.series_description',
				'checked_out', 'series.checked_out',
				'checked_out_time', 'series.checked_out_time',
				'language', 'series.language',
				'hits', 'series.hits',
				'category_title', 'c_series.category_title',
				'publish_up', 'series.publish_up',
				'publish_down', 'series.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get the category context, used by the trait
	 *
	 * @return string
	 */
	protected function getCategoryContext(): string
	{
		return 'com_sermonspeaker.series';
	}

	/**
	 * Method to get speakers for a series
	 *
	 * @param int $series Id of series
	 *
	 * @return  array
	 *
	 * @since ?
	 */
	public function getSpeakers($series)
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('DISTINCT sermons.speaker_id, speakers.title as speaker_title, speakers.pic, speakers.state');
		$query->select('speakers.intro, speakers.bio, speakers.website');
		$query->select('speakers.catid as speaker_catid, speakers.language as speaker_language');
		$query->select('CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug');
		$query->from('#__sermon_sermons AS sermons');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id');
		$query->where('sermons.state = 1');
		$query->where('sermons.speaker_id != 0');
		$query->where('sermons.series_id = ' . (int) $series);
		$query->order('speakers.title ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to check if there are Tags assigned to the speakers
	 *
	 * @return  boolean
	 *
	 * @since 6.0.0
	 */
	public function getTags()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('1');
		$query->from('`#__contentitem_tag_map`');
		$query->where("`type_alias` = 'com_sermonspeaker.serie'");

		$db->setQuery($query, 0, 1);
		$count = $db->loadResult();

		return ($count > 0);
	}

	/**
	 * Method to get an object implementing QueryInterface for retrieving the data set from a database.
	 *
	 * @return  QueryInterface|string  An object implementing QueryInterface to retrieve the data set.
	 *
	 * @since ?
	 */
	protected function getListQuery()
	{
		$user   = Factory::getApplication()->getIdentity();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select required fields from the table
		$query->select(
			$this->getState(
				'list.select',
				'series.id, series.title, series.catid, series.avatar, series.language, '
				. 'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as slug, '
				. 'series.hits, series.series_description, series.alias, series.checked_out, series.checked_out_time, '
				. 'series.state, series.ordering, series.created, series.created_by, '
				. 'series.publish_up, series.publish_down, series.zip_dl'
			)
		);
		$query->from('`#__sermon_series` AS series');

		// Join over Series Category.
		$query->select('c_series.title AS category_title');
		$query->select('CASE WHEN CHAR_LENGTH(c_series.alias) THEN CONCAT_WS(\':\', c_series.id, c_series.alias) ELSE c_series.id END as catslug');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('(series.catid = 0 OR (c_series.access IN (' . $groups . ') AND c_series.published = 1))');

		// Filter by category
		if ($categoryId = $this->getState('category.id'))
		{
			if ($levels = (int) $this->getState('filter.subcategories', 0))
			{
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = ' . (int) $categoryId);

				if ($levels > 0)
				{
					$subQuery->where('sub.level <= this.level + ' . $levels);
				}

				// Add the subquery to the main query
				$query->where('(series.catid = ' . (int) $categoryId
					. ' OR series.catid IN (' . $subQuery->__toString() . '))');
			}
			else
			{
				$query->where('series.catid = ' . (int) $categoryId);
			}
		}

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = series.created_by');

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			$nowDate = Factory::getDate()->toSql();

			$query->where('(series.publish_up = IS NULL OR series.publish_up <= :publishUp)');
			$query->where('(series.publish_down = IS NULL OR series.publish_down >= :publishDown)');
			$query->bind(':publishUp', $nowDate);
			$query->bind(':publishDown', $nowDate);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(series.title LIKE ' . $search . ')');
		}

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('series.state = ' . (int) $state);
		}

		// Do not show trashed links on the front-end
		$query->where('series.state != -2');

		// Filter by speaker (needed in speaker view)
		if ($speakerId = $this->getState('speaker.id'))
		{
			$subQuerySeries = $db->getQuery(true)
				->select('1')
				->from($db->quoteName('#__sermon_sermons', 'filter_sermons'))
				->where($db->quoteName('filter_sermons.series_id') . ' = ' . $db->quoteName('series.id'))
				->where($db->quoteName('filter_sermons.speaker_id') . ' = :speakerId');

			$query->bind(':speakerId', $speakerId, ParameterType::INTEGER);

			// Filter by state
			if (is_numeric($state))
			{
				$stateInt = (int) $state;

				$subQuerySeries->where($db->quoteName('filter_sermons.state') . ' = :sermonState');
				$query->bind(':sermonState', $stateInt, ParameterType::INTEGER);
			}

			$query->where('EXISTS (' . $subQuerySeries . ')');
		}

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('series.language in (' . $db->quote(Factory::getApplication()->getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');

		if (is_numeric($tagId))
		{
			$tagId = (int) $tagId;

			$query->join(
				'INNER', $db->quoteName('#__contentitem_tag_map', 'tagmap')
				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('series.id')
				. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_sermonspeaker.serie')
			)
				->where($db->quoteName('tagmap.tag_id') . ' = :tagId')
				->bind(':tagId', $tagId, ParameterType::INTEGER);
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state
	 *
	 * Note. Calling getState in this method will result in recursion
	 *
	 * @param string $ordering  Ordering column
	 * @param string $direction 'ASC' or 'DESC'
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);
		$jinput = $app->input;

		// Category filter (priority on request so subcategories work)
		$MenuCatid = $params->get('series_catid', $params->get('catid', 0));
		$id        = $jinput->get('catid', $MenuCatid, 'int');
		$this->setState('category.id', $id);

		// Include Subcategories or not
		$this->setState('filter.subcategories', $params->get('show_subcategory_content', 0));

		$user = Factory::getApplication()->getIdentity();

		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			// Filter on published for those who do not have edit or edit.state rights
			$this->setState('filter.state', 1);
		}

		$this->setState('filter.language', $app->getLanguageFilter());

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter-search', '', 'STRING');
		$this->setState('filter.search', $search);

		// Speakerfilter
		if ($jinput->get('view') == 'speaker')
		{
			$id = $app->getUserStateFromRequest($this->context . '.filter.speaker', 'id', 0, 'INT');
			$this->setState('speaker.id', $id);
		}

		parent::populateState('ordering', 'ASC');

		$defaultLimit = $params->get('default_pagination_limit', $app->get('list_limit'));
		$limit        = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $defaultLimit, 'uint');
		$this->setState('list.limit', $limit);

		$value      = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}
}
