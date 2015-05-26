<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSermons extends JModelList
{
	protected $_item = null;

	protected $_children = null;

	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings
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
		$this->context .= '.' . JFactory::getApplication()->input->get('view', 'sermons');
	}

	/**
	 * Get the master query for retrieving a list of items subject to the model state
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$user   = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select required fields from the table
		$query->select(
			$this->getState(
				'list.select',
				'sermons.id, sermons.title, sermons.catid, sermons.audiofile, sermons.videofile, '
				. 'sermons.custom1, sermons.custom2, sermons.audiofilesize, sermons.videofilesize, '
				. 'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug,'
				. 'sermons.picture, sermons.hits, sermons.notes, sermons.checked_out, sermons.checked_out_time,'
				. 'sermons.sermon_date, sermons.alias, sermons.sermon_time,'
				. 'sermons.state, sermons.ordering, sermons.podcast,'
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
		$query->group('sermons.id');

		// Join over Speaker
		$query->select('speakers.title AS speaker_title, speakers.pic AS pic, speakers.state as speaker_state');
		$query->select('speakers.id AS speaker_id, speakers.intro, speakers.bio, speakers.website');
		$query->select('CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over Series
		$query->select(
			'series.title AS series_title, series.state as series_state, series.avatar, '
			. 'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
		);
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
				$query->where('(' . $this->getState('category.type', 'sermons') . '.catid = ' . (int) $categoryId
					. ' OR ' . $this->getState('category.type', 'sermons') . '.catid IN (' . $subQuery->__toString() . '))');
			}
			else
			{
				$query->where($this->getState('category.type', 'sermons') . '.catid = ' . (int) $categoryId);
			}
		}

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = sermons.created_by');

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
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
		$book = $this->getState('scripture.book');

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

		// Do not show trashed links on the front-end
		$query->where('sermons.state != -2');

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
			$query->where('sermons.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if ($this->getState('list.ordering') == 'book')
		{
			$dir	= $db->escape($this->getState('list.direction', 'ASC'));
			$query->order($db->escape('book') . ' ' . $dir . ', ' . $db->escape('cap1') . ' ' . $dir . ', ' . $db->escape('vers1') . ' ' . $dir);
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$this->setState('params', $params);
		$jinput = $app->input;

		// Category filter
		$id   = $jinput->get('catid', $params->get('catid', 0), 'int');
		$type = $params->get('count_items_type', 'sermons');
		$this->setState('category.id', $id);
		$this->setState('category.type', $type);

		// Filetype filter
		$filetype = $params->get('filetype', '');
		$this->setState('filter.filetype', $filetype);

		// Include Subcategories or not
		$this->setState('filter.subcategories', $params->get('show_subcategory_content', 0));

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) &&  (!$user->authorise('core.edit', 'com_sermonspeaker')))
		{
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.state', 1);
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
			$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter-search', '', 'STRING');
			$this->setState('filter.search', $search);

			// Scripture filter
			$book = $app->getUserStateFromRequest($this->context . '.scripture.book', 'book', 0, 'INT');
			$this->setState('scripture.book', $book);

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
	 */
	protected function getStoreId($id = '')
	{
		// Add the series and speaker id to the store id.
		$id .= ':' . $this->getState('speaker.id');
		$id .= ':' . $this->getState('serie.id');
		$id .= ':' . $this->getState('category.id');
		$id .= ':' . $this->getState('category.type');

		return parent::getStoreId($id);
	}

	/**
	 * Method to get the available months
	 *
	 * @return  array  Array of mears
	 */
	public function getMonths()
	{
		$months = array(
			1 => 'JANUARY',
			2 => 'FEBRUARY',
			3 => 'MARCH',
			4 => 'APRIL',
			5 => 'MAY',
			6 => 'JUNE',
			7 => 'JULY',
			8 => 'AUGUST',
			9 => 'SEPTEMBER',
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

		$db->setQuery($query, 0);
		$options = $db->loadAssocList();

		foreach ($options as &$option)
		{
			$option['text']	= $months[$option['value']];
		}

		return $options;
	}

	/**
	 * Method to get the available years
	 *
	 * @return  array  Array of years
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

		$db->setQuery($query, 0);
		$options = $db->loadAssocList();

		return $options;
	}

	/**
	 * Method to get the available books
	 *
	 * @return  array  Array of books
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

		$db->setQuery($query, 0);
		$options = $db->loadColumn();

		return $options;
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @return  object
	 */
	public function getCategory()
	{
		if (!is_object($this->_item))
		{
			if (isset($this->state->params))
			{
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories', 0);
			}
			else
			{
				$options['countItems'] = 0;
			}

			$options['table'] = '#__sermon_' . $this->state->get('category.type', 'sermons');

			$categories = JCategories::getInstance('Sermonspeaker', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions
			if (is_object($this->_item))
			{
				$user	= JFactory::getUser();
				$userId	= $user->get('id');
				$asset	= 'com_sermonspeaker.category.' . $this->_item->id;

				// Check general create permission
				if ($user->authorise('core.create', $asset))
				{
					$this->_item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;

				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Get the parent category
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 */
	public function getParent()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 */
	public function &getLeftSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 */
	public function &getRightSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 */
	public function &getChildren()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->_children))
		{
			$params = $this->getState()->get('params');

			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha')
			{
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}
}
