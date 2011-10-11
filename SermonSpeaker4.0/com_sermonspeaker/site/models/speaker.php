<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * @package		SermonSpeaker
 */
// Based on com_contact
class SermonspeakerModelSpeaker extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'sermon_number', 'sermons.sermon_number',
				'sermon_title', 'sermons.sermon_title',
				'sermon_scripture', 'sermons.sermon_scripture',
				'sermon_date', 'sermons.sermon_date',
				'sermon_time', 'sermons.sermon_time',
				'addfileDesc', 'sermons.addfileDesc',
				'hits', 'sermons.hits', 'series.hits',
				'ordering', 'sermons.ordering',
				'name', 'speakers.name',
				'series_title', 'series.series_title',
				'series_description', 'series.series_description',
			);
		}

		parent::__construct($config);
	}

	protected function getListQuery()
	{
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->authorisedLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		if ($this->getState('speaker.layout') == 'latest-sermons'){
			// Select required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'sermons.id, sermon_number, sermon_scripture, sermon_title, sermon_time, sermons.hits, ' .
					'audiofile, videofile, picture, notes, sermon_date, addfile, addfileDesc, series.id as series_id, series_title, ' .
					'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug'
				)
			);
			$query->from('`#__sermon_sermons` AS sermons');

			// Join over Series
			$query->select(
				'series.series_title AS series_title, series.state as series_state, ' .
				'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
			);
			$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

			// Join over Speaker (for Flashplayer)
			$query->select(
				'speakers.name, speakers.pic'
			);
			$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id')) {
				$query->where('sermons.speaker_id = '.(int) $speakerId);
			}

			// Filter by search in title or scripture (with ref:)
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				if (stripos($search, 'ref:') === 0) {
					$search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
					$query->where('(sermons.sermon_scripture LIKE '.$search.')');
				} else {
					$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
					$query->where('(sermons.sermon_title LIKE '.$search.')');
				}
			}

			// Join over Sermons Category.
			$query->join('LEFT', '#__categories AS c_sermons ON c_sermons.id = sermons.catid');
			if ($categoryId = $this->getState('sermons_category.id')) {
				$query->where('sermons.catid = '.(int) $categoryId);
			}
			$query->where('(sermons.catid = 0 OR (c_sermons.access IN ('.$groups.') AND c_sermons.published = 1))');

			// Join over Series Category.
			$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
			if ($categoryId = $this->getState('series_category.id')) {
				$query->where('series.catid = '.(int) $categoryId);
			}
			$query->where('(sermons.series_id = 0 OR series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');

			// Filter by state
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->where('sermons.state = '.(int) $state);
			}

			// Add the list ordering clause.
			$query->order($db->getEscaped($this->getState('list.ordering', 'ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		} else {
			// Select required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'series.id, series_title, series_description, avatar, series.hits, ' .
					'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as slug'
				)
			);
			$query->from('`#__sermon_series` AS series');

			// Join over Sermons
			$query->join('LEFT', '#__sermon_sermons AS sermons ON sermons.series_id = series.id');

			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id')) {
				$query->where('sermons.speaker_id = '.(int) $speakerId);
			}

			// Join over Series Category.
			$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
			if ($categoryId = $this->getState('series_category.id')) {
				$query->where('series.catid = '.(int) $categoryId);
			}
			$query->where('(series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');

			// Filter by state
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->where('series.state = '.(int) $state);
				$query->where('sermons.state = '.(int) $state);
			}
			
			// Group by id
			$query->group('series.id');

			// Add the list ordering clause.
			$query->order($db->getEscaped($this->getState('list.ordering', 'ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		}
		return $query;
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
		$app	= JFactory::getApplication();
		$params	= $app->getParams();
		$this->setState('params', $params);

		$id = (int)$params->get('sermon_cat', 0);
		if (!$id){ $id = JRequest::getInt('sermon_cat', 0); }
		$this->setState('sermons_category.id', $id);

		$id = (int)$params->get('series_cat', 0);
		if (!$id){ $id = JRequest::getInt('series_cat', 0); }
		$this->setState('series_category.id', $id);

		$id = $app->getUserStateFromRequest($this->context.'.filter.speaker', 'id', 0, 'INT');
		$this->setState('speaker.id', $id);

		$this->setState('filter.state',	1);

		if (JRequest::getCmd('layout') == 'latest-sermons'){
			$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter-search', '', 'STRING');
			$this->setState('filter.search', $search);

			$order	= $params->get('default_order', 'ordering');
			$dir	= $params->get('default_order_dir', 'ASC');
			parent::populateState('sermons.'.$order, $dir);
		} else {
			parent::populateState('ordering', 'ASC');
		}
	}
	
	function getSpeaker()
	{
		$database =& JFactory::getDBO();
		$query 	= "SELECT speaker.*, c.access AS category_access, \n"
				. "CASE WHEN CHAR_LENGTH(speaker.alias) THEN CONCAT_WS(':', speaker.id, speaker.alias) ELSE speaker.id END as slug \n"
				. "FROM #__sermon_speakers as speaker \n"
				. "LEFT JOIN #__categories AS c ON c.id = speaker.catid \n"
				. "WHERE speaker.id='".$this->getState('speaker.id')."' \n"
				. "AND speaker.state = 1 \n"
				. "AND (speaker.catid = 0 OR c.published = 1)";
		$database->setQuery($query);
		$row = $database->loadObject();

       return $row;
	}

	/**
	 * Method to increment the hit counter for the speaker
	 *
	 * @param	int		Optional ID of the speaker.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function hit($id = null)
	{
		if (empty($id)) {
			$id = $this->getState('speaker.id');
		}

		$speaker = $this->getTable('Speaker', 'SermonspeakerTable');
		return $speaker->hit($id);
	}
}