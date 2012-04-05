<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SermonspeakerModelSpeakers extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'speakers.id',
				'name', 'speakers.name',
				'alias', 'speakers.alias',
				'catid', 'speakers.catid', 'category_title',
				'state', 'speakers.state',
				'access', 'speakers.access', 'access_level',
				'created', 'speakers.created',
				'created_by', 'speakers.created_by',
				'ordering', 'speakers.ordering',
				'pic', 'speakers.pic',
				'hits', 'speakers.hits',
				'home', 'speakers.home',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('speakers.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');
		$id.= ':' . $this->getState('filter.category_id');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'speakers.id, speakers.name, speakers.catid, created_by, '.
				'speakers.hits, speakers.home, speakers.pic, speakers.website, '.
				'speakers.alias, speakers.state, speakers.ordering'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = speakers.catid');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('speakers.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(speakers.state IN (0, 1))');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('speakers.catid = '.(int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('speakers.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(speakers.name LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'speakers.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', speakers.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
}