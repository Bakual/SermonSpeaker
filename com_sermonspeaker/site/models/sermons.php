<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSermons extends Listmodel
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
	 * @param   array  $config  An optional associative array of configuration settings
	 *
	 * @since ?
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'sermon_number', 'sermons.sermon_number',
				'title', 'sermons.title',
				'checked_out', 'sermons.checked_out',
				'checked_out_time', 'sermons.checked_out_time',
				'book', 'script.book',
				'sermon_date', 'sermons.sermon_date',
				'sermon_time', 'sermons.sermon_time',
				'addfileDesc', 'sermons.addfileDesc',
				'hits', 'sermons.hits',
				'language', 'sermons.language',
				'ordering', 'sermons.ordering',
				'speaker_title', 'speakers.title',
				'series_title', 'series.title',
				'category_title', 'c_sermons.category_title',
				'publish_up', 'sermons.publish_up',
				'publish_down', 'sermons.publish_down',
			);
		}

		parent::__construct($config);

		// Adding viewname to context so UserStates aren't saved accross the various views
		$this->context .= '.' . Factory::getApplication()->input->get('view', 'sermons');
	}

	/**
	 * Method to get the available months
	 *
	 * @return  array  Array of mears
	 *
	 * @since ?
	 */
	public function getMonths()
	{
		$months = array(
			1  => 'JANUARY',
			2  => 'FEBRUARY',
			3  => 'MARCH',
			4  => 'APRIL',
			5  => 'MAY',
			6  => 'JUNE',
			7  => 'JULY',
			8  => 'AUGUST',
			9  => 'SEPTEMBER',
			10 => 'OCTOBER',
			11 => 'NOVEMBER',
			12 => 'DECEMBER',
		);

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT MONTH(`sermon_date`) AS `value`');
		$query->from('`#__sermon_sermons`');
		$query->where("`sermon_date` != '0000-00-00'");

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('state = ' . (int) $state);
		}

		$query->order('`value` ASC');

		$db->setQuery($query);
		$options = $db->loadAssocList();

		foreach ($options as &$option)
		{
			$option['text'] = $months[$option['value']];
		}

		return $options;
	}

	/**
	 * Method to get the available years
	 *
	 * @return  array  Array of years
	 *
	 * @since ?
	 */
	public function getYears()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT YEAR(`sermon_date`) AS `year`');
		$query->from('`#__sermon_sermons`');
		$query->where("`sermon_date` != '0000-00-00'");

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('state = ' . (int) $state);
		}

		$query->order('`year` ASC');

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Method to get the available books
	 *
	 * @return  array  Array of books
	 *
	 * @since ?
	 */
	public function getBooks()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT `book`');
		$query->from('`#__sermon_scriptures`');
		$query->join('LEFT', '`#__sermon_sermons` AS `sermons` ON `sermon_id` = `sermons`.`id`');
		$query->where('`book` != 0');

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('`sermons`.`state` = ' . (int) $state);
		}

		$query->order('`book` ASC');

		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Method to check if there are Tags assigned to the sermons
	 *
	 * @return  boolean
	 *
	 * @since 5.9.7
	 */
	public function getTags()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(1)');
		$query->from('`#__contentitem_tag_map`');
		$query->where("`type_alias` = 'com_sermonspeaker.sermon'");

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
				$params                = $this->state->params;
				$options               = array();
				$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories', 0);
				$options['access']     = $params->get('check_access_rights', 1);
			}
			else
			{
				$options['countItems'] = 0;
			}

			$categories = Categories::getInstance('com_sermonspeaker.sermons', $options);
			$this->item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions
			if (is_object($this->item))
			{
				$user  = Factory::getUser();
				$asset = 'com_sermonspeaker.sermons.category.' . $this->item->id;

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
				$this->children = Joomla\Utilities\ArrayHelper::sortObjects($this->children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->children;
	}

	/**
	 * Get the master query for retrieving a list of items subject to the model state
	 *
	 * @return  DatabaseQuery
	 *
	 * @since ?
	 */
	protected function getListQuery()
	{
		$user   = Factory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the table
		$query->select(
			$this->getState(
				'list.select',
				'sermons.id, sermons.title, sermons.catid, sermons.audiofile, sermons.videofile, '
				. 'sermons.audiofilesize, sermons.videofilesize, '
				. 'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug,'
				. 'sermons.picture, sermons.hits, sermons.notes, sermons.checked_out, sermons.checked_out_time,'
				. 'sermons.sermon_date, sermons.alias, sermons.sermon_time,'
				. 'sermons.state, sermons.ordering, sermons.podcast, sermons.language,'
				. 'sermons.sermon_number, sermons.addfile, sermons.addfileDesc,'
				. 'sermons.created, sermons.created_by,'
				. 'sermons.publish_up, sermons.publish_down'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the scriptures
		$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text '
			. 'ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
		$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = sermons.id');
		$query->group(
			'sermons.id, sermons.title, sermons.catid, sermons.audiofile, sermons.videofile, '
			. 'sermons.audiofilesize, sermons.videofilesize, '
			. 'sermons.picture, sermons.hits, sermons.notes, sermons.checked_out, sermons.checked_out_time,'
			. 'sermons.sermon_date, sermons.alias, sermons.sermon_time,'
			. 'sermons.state, sermons.ordering, sermons.podcast, sermons.language,'
			. 'sermons.sermon_number, sermons.addfile, sermons.addfileDesc,'
			. 'sermons.created, sermons.created_by,'
			. 'sermons.publish_up, sermons.publish_down,'
			. 'speakers.title, speakers.pic, speakers.state, speakers.catid, speakers.language, '
			. 'speakers.id, speakers.alias, speakers.intro, speakers.bio, speakers.website,'
			. 'series.title, series.avatar, series.state, series.catid, series.language, series.id, series.alias,'
			. 'c_sermons.title, c_sermons.id, c_sermons.alias,'
			. 'user.name'
		);

		// Join over Speaker
		$query->select('speakers.title AS speaker_title, speakers.pic AS pic, speakers.state as speaker_state, speakers.bio, speakers.website');
		$query->select('speakers.catid AS speaker_catid, speakers.language AS speaker_language');
		$query->select('speakers.id AS speaker_id, speakers.intro, speakers.bio, speakers.website');
		$query->select('CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over Series
		$query->select('series.title AS series_title, series.state as series_state, series.avatar');
		$query->select('series.catid AS series_catid, series.language AS series_language');
		$query->select('CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug');
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Join over Sermons Category.
		$query->select('c_sermons.title AS category_title');
		$query->select('CASE WHEN CHAR_LENGTH(c_sermons.alias) THEN CONCAT_WS(\':\', c_sermons.id, c_sermons.alias) ELSE c_sermons.id END as catslug');
		$query->join('LEFT', '#__categories AS c_sermons ON c_sermons.id = sermons.catid');
		$query->where('(sermons.catid = 0 OR (c_sermons.access IN (' . $groups . ') AND c_sermons.published = 1))');

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->where('(sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN (' . $groups . ') AND c_speaker.published = 1))');

		// Join over Series Category.
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('(sermons.series_id = 0 OR series.catid = 0 OR (c_series.access IN (' . $groups . ') AND c_series.published = 1))');

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
				$query->where('(sermons.catid = ' . (int) $categoryId
					. ' OR sermons.catid IN (' . $subQuery->__toString() . '))');
			}
			else
			{
				$query->where('sermons.catid = ' . (int) $categoryId);
			}
		}

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = sermons.created_by');

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(Factory::getDate()->toSql());

			$query->where('(sermons.publish_up = ' . $nullDate . ' OR sermons.publish_up <= ' . $nowDate . ')');
			$query->where('(sermons.publish_down = ' . $nullDate . ' OR sermons.publish_down >= ' . $nowDate . ')');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(sermons.title LIKE ' . $search . ')');
		}

		// Filter by date
		$year = $this->getState('date.year');

		if ($year)
		{
			$query->where('YEAR(sermons.sermon_date) = ' . (int) $year);
		}

		$month = $this->getState('date.month');

		if ($month)
		{
			$query->where('MONTH(sermons.sermon_date) = ' . (int) $month);
		}

		// Filter by scripture
		$book = $this->getState('filter.book');

		if ($book)
		{
			$query->where('script.book = ' . (int) $book);
		}

		// Filter by filetype
		$filetype = $this->getState('filter.filetype');

		if ($filetype == 'video')
		{
			$query->where('sermons.videofile != ""');
		}
		elseif ($filetype == 'audio')
		{
			$query->where('sermons.audiofile != ""');
		}

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('sermons.state = ' . (int) $state);
		}
		else
		{
			$query->where('sermons.state != -2');
		}

		// Filter by speaker (needed in speaker view)
		if ($speakerId = $this->getState('speaker.id'))
		{
			$query->where('sermons.speaker_id = ' . (int) $speakerId);
		}

		// Filter by serie (needed in serie view)
		if ($serieId = $this->getState('serie.id'))
		{
			$query->where('sermons.series_id = ' . (int) $serieId);
		}

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('sermons.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if ($this->getState('list.ordering') == 'book')
		{
			$dir = $db->escape($this->getState('list.direction', 'ASC'));
			$query->order($db->escape('book') . ' ' . $dir . ', ' . $db->escape('cap1') . ' ' . $dir . ', ' . $db->escape('vers1') . ' ' . $dir);
		}

		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');

		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('sermons.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_sermonspeaker.sermon')
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var JApplicationSite $app */
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);
		$jinput = $app->input;

		// Category filter
		$id = $jinput->get('catid', $params->get('catid', 0), 'int');
		$this->setState('category.id', $id);

		// Filetype filter
		$filetype = $params->get('filetype', '');
		$this->setState('filter.filetype', $filetype);

		// Include Subcategories or not
		$this->setState('filter.subcategories', $params->get('show_subcategory_content', 0));

		$user = Factory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			// Filter on published or archived for those who do not have edit or edit.state rights.
			$validStates = array(1, 2);
			$state       = $jinput->get('state', 1, 'int');
			$state       = (in_array($state, $validStates)) ? $state : 1;
			$this->setState('filter.state', $state);
		}

		$this->setState('filter.language', $app->getLanguageFilter());

		$limit = (int) $params->get('limit', '');

		if ($limit)
		{
			$this->setState('list.limit', $limit);
			$this->setState('list.start', 0);
			$this->setState('list.ordering', 'sermons.sermon_date');
			$this->setState('list.direction', 'DESC');
		}
		else
		{
			// Date filter, don't use UserState here as it could be set from module without the possibility to reset it.
			// Needs additional URL params in pagination.
			$month = $app->input->getInt('month', $params->get('month'));
			$this->setState('date.month', $month);
			$year_default = ($month) ? 0 : '';
			$this->setState('date.year', $app->input->getInt('year', $params->get('year', $year_default)));

			// Speaker filter
			$speaker = $app->getUserStateFromRequest($this->context . '.speaker.id', 'speaker', 0, 'INT');
			$this->setState('speaker.id', $speaker);

			// Series filter
			$serie = $app->getUserStateFromRequest($this->context . '.serie.id', 'serie', 0, 'INT');
			$this->setState('serie.id', $serie);

			$order = $params->get('default_order', 'ordering');
			$dir   = $params->get('default_order_dir', 'ASC');

			parent::populateState($order, $dir);

			$defaultLimit = $params->get('default_pagination_limit', $app->get('list_limit'));
			$limit        = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $defaultLimit, 'uint');
			$this->setState('list.limit', $limit);

			$value      = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
		}
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since ?
	 */
	protected function getStoreId($id = '')
	{
		// Add the series and speaker id to the store id.
		$id .= ':' . $this->getState('speaker.id');
		$id .= ':' . $this->getState('serie.id');
		$id .= ':' . $this->getState('category.id');

		return parent::getStoreId($id);
	}
}
