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
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		if ($categoryId = $this->getState('series_category.id')) {
			$query->where('series.catid = '.(int) $categoryId);
		}
		$query->where('(series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('series.state = '.(int) $state);
		}

		// Add the list ordering clause.
		$query->order('series.ordering ASC');

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

		$orderCol	= JRequest::getCmd('filter_order', $params->get('default_order', 'ordering'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('default_order_dir', 'ASC'));
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
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->authorisedLevels());

		$db =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermon_title, sermon_number, sermon_time, notes, sermon_date, addfile, addfileDesc, pic, name, picture \n"
				. ", CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(':', sermons.id, sermons.alias) ELSE sermons.id END as slug \n"
				. " FROM #__sermon_sermons as sermons \n"
				. " LEFT JOIN #__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id \n"
				. " LEFT JOIN #__categories AS c_speaker ON c_speaker.id = speakers.catid \n"
				. " LEFT JOIN #__categories AS c_sermons ON c_sermons.id = sermons.catid \n"
				. " WHERE series_id=".$serieid." \n"
				. " AND (sermons.catid = 0 OR (c_sermons.access IN (".$groups.") AND c_sermons.published = 1)) \n"
				. " AND (sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN (".$groups.") AND c_speaker.published = 1)) \n"
				. " AND sermons.state = '1' \n"
				. " ORDER BY sermons.".$db->getEscaped($this->getState('list.ordering', 'ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC'));
		$db->setQuery( $query );
		$sermons = $db->loadObjectList();
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