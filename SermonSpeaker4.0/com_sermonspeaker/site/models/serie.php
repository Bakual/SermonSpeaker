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
class SermonspeakerModelSerie extends JModelList
{
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
				'sermons.id, sermons.sermon_title, sermons.catid, sermons.sermon_path, ' .
				'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug,' .
				'sermons.hits, sermons.notes, sermons.sermon_scripture,' .
				'sermons.sermon_date, sermons.alias, sermons.sermon_time,' .
				'sermons.state, sermons.ordering, sermons.podcast,' .
				'sermons.sermon_number, sermons.addfile, sermons.addfileDesc'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over Speaker
		$query->select(
			'speakers.name AS name, speakers.pic AS pic,' .
			'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug'
		);
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over Series
		$query->select(
			'series.series_title AS series_title,' .
			'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
		);
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Filter by series
		if ($seriesId = $this->getState('serie.id')) {
			$query->where('sermons.series_id = '.(int) $seriesId);
		}


		// Join over Sermons Category.
		if ($categoryId = $this->getState('sermons_category.id')) {
			$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug ');
			$query->join('LEFT', '#__categories AS c ON c.id = sermons.catid');
			$query->where('sermons.catid = '.(int) $categoryId);
			$query->where('c.access IN ('.$groups.')');
		}

		// Join over Speakers Category.
		if ($categoryId = $this->getState('speakers_category.id')) {
			$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug ');
			$query->join('LEFT', '#__categories AS c ON c.id = speakers.catid');
			$query->where('speakers.catid = '.(int) $categoryId);
			$query->where('c.access IN ('.$groups.')');
		}

		// Join over Series Category.
		if ($categoryId = $this->getState('series_category.id')) {
			$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug ');
			$query->join('LEFT', '#__categories AS c ON c.id = series.catid');
			$query->where('series.catid = '.(int) $categoryId);
			$query->where('c.access IN ('.$groups.')');
		}

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('sermons.state = '.(int) $state);
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'sermons.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

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
		$params	= JComponentHelper::getParams('com_sermonspeaker');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', 'ordering');
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		$this->setState('list.direction', $listOrder);

		$id = JRequest::getVar('sermon_cat', 0, '', 'int');
		$this->setState('sermons_category.id', $id);

		$id = JRequest::getVar('speaker_cat', 0, '', 'int');
		$this->setState('speakers_category.id', $id);

		$id = JRequest::getVar('series_cat', 0, '', 'int');
		$this->setState('series_category.id', $id);

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('serie.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	function getSerie()
	{
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__sermon_series WHERE id='".$this->getState('serie.id')."'";
		$database->setQuery($query);
		$row = $database->loadObject();

       return $row;
	}

	/**
	 * Method to increment the hit counter for the series
	 *
	 * @param	int		Optional ID of the series.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function hit($id = null)
	{
		if (empty($id)) {
			$id = $this->getState('serie.id');
		}

		$serie = $this->getTable('Serie', 'SermonspeakerTable');
		return $serie->hit($id);
	}
}