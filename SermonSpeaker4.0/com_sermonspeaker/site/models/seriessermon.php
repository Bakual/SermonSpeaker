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
class SermonspeakerModelSeriessermon extends JModelList
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
				'series.id, series.series_title, series.series_description, series.avatar, ' .
				'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as slug'
			)
		);
		$query->from('`#__sermon_series` AS series');

		// Join over Series Category.
		if ($categoryId = $this->getState('series_category.id')) {
			$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
			$query->where('series.catid = '.(int) $categoryId);
			$query->where('c_series.access IN ('.$groups.')');
		}

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('series.state = '.(int) $state);
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
		$params	= $app->getParams();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', 'ordering');
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		$this->setState('list.direction', $listOrder);

		$id = (int)$params->get('series_cat', 0);
		if (!$id){ $id = JRequest::getInt('series_cat', 0); }
		$this->setState('series_category.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}

	function getSermons($serieid)
	{
		$database =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermon_title, sermon_number, sermon_time, notes, sermon_date, addfile, addfileDesc \n"
				. ", CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. " FROM #__sermon_sermons \n"
				. " WHERE series_id=".$serieid." \n"
				. " AND state = '1' \n"
				. " ORDER BY ordering, (sermon_number+0) DESC, sermon_date DESC";
		$database->setQuery( $query );
		$sermons = $database->loadObjectList();
		return $sermons;
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