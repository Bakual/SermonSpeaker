<?php
defined('_JEXEC') or die;

class SermonspeakerModelSeries extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param  array $config An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'series.id',
				'title', 'series.title',
				'alias', 'series.alias',
				'checked_out', 'series.checked_out',
				'checked_out_time', 'series.checked_out_time',
				'catid', 'series.catid', 'category_title',
				'state', 'series.state',
				'access', 'series.access', 'access_level',
				'created', 'series.created',
				'created_by', 'series.created_by',
				'ordering', 'series.ordering',
				'avatar', 'series.avatar',
				'home', 'series.home',
				'language', 'series.language',
				'hits', 'series.hits',
				'publish_up', 'series.publish_up',
				'publish_down', 'series.publish_down',
			);

			// Searchtools
			$config['filter_fields'][] = 'category_id';
			$config['filter_fields'][] = 'level';
			$config['filter_fields'][] = 'tag';

			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param string $ordering
	 * @param string $direction
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}

		// Load the parameters.
		$params = JComponentHelper::getParams('com_sermonspeaker');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('series.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'series.id, series.title, series.catid, series.language, '
				. 'series.hits, series.home, series.alias, series.avatar, '
				. 'series.state, series.ordering, created_by, series.checked_out, series.checked_out_time, '
				. 'series.publish_up, series.publish_down'
			)
		);
		$query->from('`#__sermon_series` AS series');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = series.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = series.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = series.created_by');

		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = series.id AND asso.context=' . $db->quote('com_sermonspeaker.serie'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('series.id');
		}

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = series.catid');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('series.state = ' . (int) $published);
		}
		elseif ($published === '' || $published === null)
		{
			$query->where('(series.state IN (0, 1))');
		}

		// Filter by category.
		$baselevel  = 1;
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt       = $cat_tbl->rgt;
			$lft       = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= ' . (int) $lft)
				->where('c.rgt <= ' . (int) $rgt);
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('c.level <= ' . ((int) $level + (int) $baselevel - 1));
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('series.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(series.title LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('series.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol == 'series.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'category_title ' . $orderDirn . ', series.ordering';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
