<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSeries extends ListModel
{
	/**
	 * @var object
	 *
	 * @since ?
	 */
	private $item;

	/**
	 * @var
	 *
	 * @since ?
	 */
	private $children;

	/**
	 * @var
	 *
	 * @since ?
	 */
	private $parent;

	/**
	 * @var
	 *
	 * @since ?
	 */
	private $leftsibling;

	/**
	 * @var
	 *
	 * @since ?
	 */
	private $rightsibling;

	/**
	 * Constructor
	 *
	 * @param   array $config An optional associative array of configuration settings
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
	 * Get the master query for retrieving a list of items subject to the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since ?
	 */
	protected function getListQuery()
	{
		$user   = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the table
		$query->select(
			$this->getState(
				'list.select',
				'series.id, series.title, series.catid, series.avatar, series.language, '
				. 'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as slug, '
				. 'series.hits, series.series_description, series.alias, series.checked_out, series.checked_out_time, '
				. 'series.state, series.ordering, series.created, series.created_by, '
				. 'series.publish_up, series.publish_down'
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
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(JFactory::getDate()->toSql());

			$query->where('(series.publish_up = ' . $nullDate . ' OR series.publish_up <= ' . $nowDate . ')');
			$query->where('(series.publish_down = ' . $nullDate . ' OR series.publish_down >= ' . $nowDate . ')');
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
			// Join over Sermons
			$query->join('LEFT', '#__sermon_sermons AS sermons ON sermons.series_id = series.id');

			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id'))
			{
				$query->where('sermons.speaker_id = ' . (int) $speakerId);
			}

			// Filter by state
			if (is_numeric($state))
			{
				$query->where('sermons.state = ' . (int) $state);
			}

			// Group by id
			$query->group('series.id');
		}

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('series.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
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
	 * @param   string $ordering  Ordering column
	 * @param   string $direction 'ASC' or 'DESC'
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var JApplicationSite $app */
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);
		$jinput = $app->input;

		// Category filter (priority on request so subcategories work)
		$id = $jinput->get('catid', $params->get('catid', 0), 'int');
		$this->setState('category.id', $id);

		// Include Subcategories or not
		$this->setState('filter.subcategories', $params->get('show_subcategory_content', 0));

		$user = JFactory::getUser();

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
		$limit = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $defaultLimit, 'uint');
		$this->setState('list.limit', $limit);
	}

	/**
	 * Method to get speakers for a series
	 *
	 * @param   int $series Id of series
	 *
	 * @return  array
	 *
	 * @since ?
	 */
	public function getSpeakers($series)
	{
		$db    = $this->getDbo();
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
		$speakers = $db->loadObjectList();

		return $speakers;
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @return  object
	 *
	 * @since ?
	 */
	public function getCategory()
	{
		if (!is_object($this->item))
		{
			if (isset($this->state->params))
			{
				/** @var \Joomla\Registry\Registry $params */
				$params                = $this->state->params;
				$options               = array();
				$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories', 0);
			}
			else
			{
				$options['countItems'] = 0;
			}

			$options['table'] = '#__sermon_series';

			$categories = \JCategories::getInstance('sermonspeaker.series', $options);
			$this->item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->item))
			{
				$user  = JFactory::getUser();
				$asset = 'com_sermonspeaker.category.' . $this->item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset))
				{
					$this->item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->children = $this->item->getChildren();
				$this->parent   = false;

				if ($this->item->getParent())
				{
					$this->parent = $this->item->getParent();
				}

				$this->rightsibling = $this->item->getSibling();
				$this->leftsibling  = $this->item->getSibling(false);
			}
			else
			{
				$this->children = false;
				$this->parent   = false;
			}
		}

		return $this->item;
	}

	/**
	 * Get the parent category
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 *
	 * @since ?
	 */
	public function getParent()
	{
		if (!is_object($this->item))
		{
			$this->getCategory();
		}

		return $this->parent;
	}

	/**
	 * Get the left sibling (adjacent) categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 *
	 * @since ?
	 */
	public function &getLeftSibling()
	{
		if (!is_object($this->item))
		{
			$this->getCategory();
		}

		return $this->leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 *
	 * @since ?
	 */
	public function &getRightSibling()
	{
		if (!is_object($this->item))
		{
			$this->getCategory();
		}

		return $this->rightsibling;
	}

	/**
	 * Get the child categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 *
	 * @since ?
	 */
	public function &getChildren()
	{
		if (!is_object($this->item))
		{
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->children))
		{
			/** @var \Joomla\Registry\Registry $params */
			$params = $this->getState()->get('params');

			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha')
			{
				$this->children = Joomla\Utilities\ArrayHelper::sortObjects($this->children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->children;
	}
}
