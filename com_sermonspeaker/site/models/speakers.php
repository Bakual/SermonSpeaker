<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelspeakers extends ListModel
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
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since ?
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering', 'speakers.ordering',
				'title', 'speakers.title',
				'intro', 'speakers.intro',
				'bio', 'speakers.bio',
				'checked_out', 'speakers.checked_out',
				'checked_out_time', 'speakers.checked_out_time',
				'language', 'speakers.language',
				'hits', 'speakers.hits',
				'category_title', 'c_speakers.category_title',
				'publish_up', 'speakers.publish_up',
				'publish_down', 'speakers.publish_down',
			);
		}

		parent::__construct($config);
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
		$query->select('COUNT(1)');
		$query->from('`#__contentitem_tag_map`');
		$query->where("`type_alias` = 'com_sermonspeaker.speaker'");

		$db->setQuery($query, 0);
		$count = $db->loadResult();

		return ($count > 0);
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
				$options['countItems'] = $params->get('show_cat_numitems', 1) || !$params->get('show_empty_categories', 0);
			}
			else
			{
				$options['countItems'] = 0;
			}

			$options['table'] = '#__sermon_' . $this->state->get('category.type', 'speakers');

			$categories = Categories::getInstance('sermonspeaker.speakers', $options);
			$this->item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions
			if (is_object($this->item))
			{
				$user  = Factory::getUser();
				$asset = 'com_sermonspeaker.speakers.category.' . $this->item->id;

				// Check general create permission
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
				$this->children = ArrayHelper::sortObjects($this->children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->children;
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
		$user   = Factory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'DISTINCT speakers.id, speakers.title, speakers.catid, speakers.pic, '
				. 'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug, '
				. 'speakers.hits, speakers.intro, speakers.bio, speakers.website, speakers.alias, '
				. 'speakers.checked_out, speakers.checked_out_time, '
				. 'speakers.state, speakers.ordering, speakers.created, speakers.created_by, '
				. 'speakers.publish_up, speakers.publish_down, '
				. 'speakers.language'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over Speakers Category.
		$query->select('c_speaker.title AS category_title');
		$query->select('CASE WHEN CHAR_LENGTH(c_speaker.alias) THEN CONCAT_WS(\':\', c_speaker.id, c_speaker.alias) ELSE c_speaker.id END as catslug');
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->where('(speakers.catid = 0 OR (c_speaker.access IN (' . $groups . ') AND c_speaker.published = 1))');

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
				$query->where('(speakers.catid = ' . (int) $categoryId
					. ' OR speakers.catid IN (' . $subQuery->__toString() . '))'
				);
			}
			else
			{
				$query->where('speakers.catid = ' . (int) $categoryId);
			}
		}

		// Subquery to get counts of sermons and series
		$query->select('(SELECT COUNT(DISTINCT sermons.id) FROM #__sermon_sermons AS sermons '
			. 'WHERE sermons.speaker_id = speakers.id AND sermons.id > 0 AND sermons.state = 1) AS sermons'
		);
		$query->select('(SELECT COUNT(DISTINCT sermons2.series_id) FROM #__sermon_sermons AS sermons2 '
			. 'WHERE sermons2.speaker_id = speakers.id AND sermons2.series_id > 0 AND sermons2.state = 1) AS series'
		);

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = speakers.created_by');

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(Factory::getDate()->toSql());

			$query->where('(speakers.publish_up = ' . $nullDate . ' OR speakers.publish_up <= ' . $nowDate . ')');
			$query->where('(speakers.publish_down = ' . $nullDate . ' OR speakers.publish_down >= ' . $nowDate . ')');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if ($search)
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(speakers.title LIKE ' . $search . ')');
		}

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('speakers.state = ' . (int) $state);
		}
		// Do not show trashed links on the front-end
		$query->where('speakers.state != -2');

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('speakers.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');

		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('speakers.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_sermonspeaker.speaker')
				);
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Ordering column
	 * @param   string  $direction  'ASC' or 'DESC'
	 *
	 * @return  void
	 *
	 * @since ?
	 *
	 * @since ?
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var JApplicationSite $app */
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);

		// Category filter (priority on request so subcategories work)
		$id = $app->input->get('catid', $params->get('catid', 0), 'int');
		$this->setState('category.id', $id);

		// Include Subcategories or not
		$this->setState('filter.subcategories', $params->get('show_subcategory_content', 0));

		$user = Factory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.state', 1);
		}

		$this->setState('filter.language', $app->getLanguageFilter());

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter-search', '', 'STRING');
		$this->setState('filter.search', $search);

		parent::populateState('ordering', 'ASC');

		$defaultLimit = $params->get('default_pagination_limit', $app->get('list_limit'));
		$limit        = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $defaultLimit, 'uint');
		$this->setState('list.limit', $limit);

		$value      = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}
}
