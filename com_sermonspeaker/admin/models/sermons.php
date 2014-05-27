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
				'title', 'sermons.title',
				'alias', 'sermons.alias',
				'checked_out', 'sermons.checked_out',
				'checked_out_time', 'sermons.checked_out_time',
				'speaker_title', 'speakers.title',
				'catid', 'sermons.catid', 'category_title',
				'state', 'sermons.state',
				'podcast', 'sermons.podcast',
				'access', 'sermons.access', 'access_level',
				'created', 'sermons.created',
				'created_by', 'sermons.created_by',
				'ordering', 'sermons.ordering',
				'sermon_date', 'sermons.sermon_date',
				'language', 'sermons.language',
				'hits', 'sermons.hits',
				'series_title', 'series.title',
				'scripture', 'sermons.scripture',
			);

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

		$podcast = $app->getUserStateFromRequest($this->context.'.filter.podcast', 'filter_podcast', '', 'string');
		$this->setState('filter.podcast', $podcast);

		$speaker = $app->getUserStateFromRequest($this->context.'.filter.speaker', 'filter_speaker', '', 'string');
		$this->setState('filter.speaker', $speaker);

		$series = $app->getUserStateFromRequest($this->context.'.filter.series', 'filter_series', '', 'string');
		$this->setState('filter.series', $series);

		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// force a language
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
		$id.= ':' . $this->getState('filter.language');

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
				'sermons.id, sermons.title, sermons.catid, sermons.language, '.
				'sermons.hits, sermons.notes, sermons.checked_out, sermons.checked_out_time, '.
				'sermons.sermon_date, sermons.alias, sermons.created, sermons.created_by, '.
				'sermons.state, sermons.ordering, sermons.podcast'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = sermons.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = sermons.checked_out');

		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = sermons.id AND asso.context=' . $db->quote('com_sermonspeaker.sermon'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('sermons.id');
		}

		// Join over the scriptures.
		$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
		$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = sermons.id');
		$query->group('sermons.id');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = sermons.catid');

		// Join over the speakers.
		$query->select('speakers.title AS speaker_title');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over the series.
		$query->select('series.title AS series_title');
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
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(sermons.title LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('sermons.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'sermons.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', sermons.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	public function getSpeakers()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('speakers.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.title, " (", c_speakers.title, ")") ELSE speakers.title END AS text');
		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 1');
		$query->order('speakers.title');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query	= $db->getQuery(true);

		$query->select('speakers.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.title, " (", c_speakers.title, ")") ELSE speakers.title END AS text');
		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 0');
		$query->order('speakers.title');

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
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
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
		$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.title, " (", c_series.title, ")") ELSE series.title END AS text');
		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 1');
		$query->order('series.title');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query	= $db->getQuery(true);

		$query->select('series.id As value');
		$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.title, " (", c_series.title, ")") ELSE series.title END AS text');
		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 0');
		$query->order('series.title');

		// Get the options.
		$db->setQuery($query);

		$unpublished = $db->loadObjectList();
		if (count($unpublished))
		{
			if (count($published))
			{
				array_unshift($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
				array_push($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
			}

			array_unshift($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
			array_push($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
		}

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		$options = array_merge($published, $unpublished);

		return $options;
	}
}