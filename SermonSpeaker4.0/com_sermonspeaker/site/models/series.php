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
				'series.state, series.ordering'
			)
		);
		$query->from('`#__sermon_series` AS series');

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
			$query->where('series.state = '.(int) $state);
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'series.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

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

		$id = JRequest::getVar('series_cat', 0, '', 'int');
		$this->setState('series_category.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}

	function getSpeakers($series)
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT sermons.speaker_id, speakers.name, speakers.pic'
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

	function getCat()
	{
		$database =& JFactory::getDBO();
		$cats[] = $this->getState('series_category.id');
		$cats = array_unique($cats);
		$title = array();
		foreach ($cats as $cat){
			$query = "SELECT title FROM #__categories WHERE id = ".$cat;
			$database->setQuery( $query );
			$title[] = $database->LoadResult();
		}
		$title = implode(' &amp; ', $title);
		return $title;
	}
}