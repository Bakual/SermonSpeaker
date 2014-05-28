<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SermonspeakerModelSpeakers extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 * @see    JController
	 * @since  1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'speakers.id',
				'title', 'speakers.title',
				'alias', 'speakers.alias',
				'checked_out', 'speakers.checked_out',
				'checked_out_time', 'speakers.checked_out_time',
				'catid', 'speakers.catid', 'category_title',
				'state', 'speakers.state',
				'access', 'speakers.access', 'access_level',
				'created', 'speakers.created',
				'created_by', 'speakers.created_by',
				'ordering', 'speakers.ordering',
				'pic', 'speakers.pic',
				'language', 'speakers.language',
				'hits', 'speakers.hits',
				'home', 'speakers.home',
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
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('speakers.title', 'asc');
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
				'speakers.id, speakers.title, speakers.catid, speakers.created_by, speakers.language, '.
				'speakers.hits, speakers.home, speakers.pic, speakers.website, '.
				'speakers.alias, speakers.state, speakers.ordering, speakers.checked_out, speakers.checked_out_time'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = speakers.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = speakers.checked_out');

		// Join over the associations.
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = speakers.id AND asso.context=' . $db->quote('com_sermonspeaker.speaker'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('speakers.id');
		}

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
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(speakers.title LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('speakers.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'speakers.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', speakers.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}