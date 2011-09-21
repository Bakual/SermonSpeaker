<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SermonspeakerModelSermons extends JModelList
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
				'id', 'sermons.id',
				'sermon_title', 'sermons.sermon_title',
				'alias', 'sermons.alias',
				'name', 'speakers.name',
				'catid', 'sermons.catid', 'category_title',
				'state', 'sermons.state',
				'podcast', 'sermons.podcast',
				'access', 'sermons.access', 'access_level',
				'created', 'sermons.created',
				'created_by', 'sermons.created_by',
				'ordering', 'sermons.ordering',
				'sermon_date', 'sermons.sermon_date',
				'hits', 'sermons.hits',
				'series_title', 'series.series_title',
				'sermon_scripture', 'sermons.sermon_scripture',
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
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$podcast = $app->getUserStateFromRequest($this->context.'.filter.podcast', 'filter_podcast', '', 'string');
		$this->setState('filter.podcast', $podcast);

		$speaker = $app->getUserStateFromRequest($this->context.'.filter.speaker', 'filter_speaker', '', 'string');
		$this->setState('filter.speaker', $speaker);

		$series = $app->getUserStateFromRequest($this->context.'.filter.series', 'filter_series', '', 'string');
		$this->setState('filter.series', $series);

		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_sermonspeaker');
		$this->setState('params', $params);

		// List state information.
		$order		= $params->get('default_order', 'ordering');
		$orderDir	= $params->get('default_order_dir', 'ASC');
		parent::populateState('sermons.'.$order, $orderDir);
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
		$id.= ':' . $this->getState('filter.podcast');
		$id.= ':' . $this->getState('filter.speaker');
		$id.= ':' . $this->getState('filter.series');
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
				'sermons.id, sermons.sermon_title, sermons.catid, '.
				'sermons.hits, sermons.notes, sermons.sermon_scripture, '.
				'sermons.sermon_date, sermons.alias, sermons.created, sermons.created_by, '.
				'sermons.state, sermons.ordering, sermons.podcast'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = sermons.catid');

		// Join over the speakers.
		$query->select('speakers.name AS name');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over the series.
		$query->select('series.series_title AS series_title');
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('sermons.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(sermons.state IN (0, 1))');
		}

		// Filter by podcast state
		$podcast = $this->getState('filter.podcast');
		if (is_numeric($podcast)) {
			$query->where('sermons.podcast = '.(int) $podcast);
		}

		// Filter by speaker
		$speaker = $this->getState('filter.speaker');
		if (is_numeric($speaker)) {
			$query->where('sermons.speaker_id = '.(int) $speaker);
		}

		// Filter by series
		$series = $this->getState('filter.series');
		if (is_numeric($series)) {
			$query->where('sermons.series_id = '.(int) $series);
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('sermons.catid = '.(int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('sermons.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(sermons.sermon_title LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'sermons.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', sermons.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}

	public function getSpeakers()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('speakers.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.name, " (", c_speakers.title, ")") ELSE speakers.name END AS text');
		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 1');
		$query->order('speakers.name');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query	= $db->getQuery(true);

		$query->select('speakers.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.name, " (", c_speakers.title, ")") ELSE speakers.name END AS text');
		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 0');
		$query->order('speakers.name');

		// Get the options.
		$db->setQuery($query);

		$unpublished = $db->loadObjectList();
		if (count($unpublished)){
			if (count($published)){
				array_unshift($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
				array_push($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
			}
			array_unshift($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
			array_push($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
		}
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		$options = array_merge($published, $unpublished);

		return $options;
	}

	public function getSeries()
	{
		// Initialize variables.
		$options = array();

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('series.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.series_title, " (", c_series.title, ")") ELSE series.series_title END AS text');
		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 1');
		$query->order('series.series_title');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query	= $db->getQuery(true);

		$query->select('series.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.series_title, " (", c_series.title, ")") ELSE series.series_title END AS text');
		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 0');
		$query->order('series.series_title');

		// Get the options.
		$db->setQuery($query);

		$unpublished = $db->loadObjectList();
		if (count($unpublished)){
			if (count($published)){
				array_unshift($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
				array_push($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
			}
			array_unshift($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
			array_push($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
		}
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		$options = array_merge($published, $unpublished);

		return $options;
	}
}