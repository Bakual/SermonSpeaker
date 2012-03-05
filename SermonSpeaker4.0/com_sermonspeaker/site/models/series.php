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
class SermonspeakerModelSeries extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'series_title', 'series.series_title',
				'ordering', 'series.ordering',
				'series_description', 'series.series_description',
				'hits', 'series.hits',
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

		// Select required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'series.id, series.series_title, series.catid, series.avatar, ' .
				'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as slug, ' .
				'series.hits, series.series_description, series.alias, ' .
				'series.state, series.ordering, series.created, series.created_by'
			)
		);
		$query->from('`#__sermon_series` AS series');

		// Join over Series Category.
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		if ($categoryId = $this->getState('series_category.id')) {
			$query->where('series.catid = '.(int) $categoryId);
		}
		$query->where('(series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = series.created_by');

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('series.state = '.(int) $state);
		}

		// Filter by speaker (needed in speaker view)
		if ($speakerId = $this->getState('speaker.id')) {
			// Join over Sermons
			$query->join('LEFT', '#__sermon_sermons AS sermons ON sermons.series_id = series.id');
			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id')) {
				$query->where('sermons.speaker_id = '.(int) $speakerId);
			}
			// Filter by state
			if (is_numeric($state)) {
				$query->where('sermons.state = '.(int) $state);
			}
			// Group by id
			$query->group('series.id');
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

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
		$app	= JFactory::getApplication();
		$params	= $app->getParams();
		$this->setState('params', $params);

		$id = (int)$params->get('series_cat', 0);
		if (!$id){ $id = JRequest::getInt('series_cat', 0); }
		$this->setState('series_category.id', $id);

		$this->setState('filter.state',	1);

		// Speakerfilter
		if(JRequest::getCmd('view') == 'speaker'){
			$id = $app->getUserStateFromRequest($this->context.'.filter.speaker', 'id', 0, 'INT');
			$this->setState('speaker.id', $id);
		}

		parent::populateState('ordering', 'ASC');
	}

	function getSpeakers($series)
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT sermons.speaker_id, speakers.name, speakers.pic, '
		. ' CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug'
        . ' FROM #__sermon_sermons AS sermons'
		. ' LEFT JOIN #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id'
        . " WHERE sermons.state = '1'"
        . " AND speakers.state = '1'"
		. " AND sermons.series_id = '".$series."'"
        . ' GROUP BY sermons.speaker_id'
        . ' ORDER BY speakers.name';
		$db->setQuery($query);
		$speakers = $db->loadObjectList();

		return $speakers;
	}

	/**
	 * Method to get the name of the category.
	 *
	 * @since	1.6
	 */
	public function getCat()
	{
		$categoryId = $this->getState('series_category.id');
		if (!$categoryId) { 
			return false; 
		}
		$db		= $this->getDbo();
		$query = "SELECT title FROM #__categories WHERE id = ".$categoryId;
		$db->setQuery($query);
		$title = $db->LoadResult();
		return $title;
	}
}