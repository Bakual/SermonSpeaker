<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SermonspeakerModelSermons extends JModelList
{
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
		parent::populateState('sermons.sermon_title', 'asc');
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
				'sermons.id, sermons.sermon_title, sermons.catid,' .
				'sermons.hits, sermons.notes, sermons.sermon_scripture,' .
				'sermons.sermon_date, sermons.alias,' .
				'sermons.published, sermons.ordering, sermons.podcast'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = sermons.catid');

		// Join over the speakers.
		$query->select('speakers.name AS name');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over the speakers.
		$query->select('series.series_title AS series_title');
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('sermons.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(sermons.published IN (0, 1))');
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

		// Filter by speaker
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
		// Create a new query object.
		$db		= $this->getDbo();

		$query	= "SELECT speakers.id, speakers.name \n"
				. "FROM `#__sermon_speakers` AS speakers \n"
				. "ORDER BY speakers.name ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getSeries()
	{
		// Create a new query object.
		$db		= $this->getDbo();

		$query	= "SELECT series.id, series.series_title \n"
				. "FROM `#__sermon_series` AS series \n"
				. "ORDER BY series.series_title ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}