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
					'sermons.id as sermons_id, sermon_number, sermon_scripture, sermon_title, sermon_time, ' .
					'audiofile, videofile, picture, notes, sermon_date, addfile, addfileDesc, series.id as series_id, series_title, ' .
					'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug'
				)
			);
			$query->from('`#__sermon_sermons` AS sermons');

			// Join over Series
			$query->select(
				'series.series_title AS series_title,' .
				'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
			);
			$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id')) {
				$query->where('sermons.speaker_id = '.(int) $speakerId);
			}

			// Filter by state
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->where('sermons.state = '.(int) $state);
			}

			// Add the list ordering clause.
			$query->order($db->getEscaped($this->getState('list.ordering', 'sermons.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
		} else {
			// Select required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'series.id, series_title, series_description, avatar, ' .
					'audiofile, videofile, notes, sermon_date, addfile, addfileDesc, series.id as series_id, series_title, ' .
					'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug'
				)
			);
			$query->from('`#__sermon_series` AS series');

			// Join over Sermons
			$query->join('LEFT', '#__sermon_sermons AS sermons ON sermons.series_id = series.id');

			// Filter by speaker
			if ($speakerId = $this->getState('speaker.id')) {
				$query->where('sermons.speaker_id = '.(int) $speakerId);
			}

			// Filter by state
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->where('series.state = '.(int) $state);
				$query->where('sermons.state = '.(int) $state);
			}
			
			// Group by id
			$query->group('series.id');

			// Add the list ordering clause.
			$query->order($db->getEscaped($this->getState('list.ordering', 'series.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));
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

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', $params->get('default_order', 'ordering'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('default_order_dir', 'ASC'));
		$this->setState('list.direction', $listOrder);

		$id = JRequest::getVar('sermon_cat', 0, '', 'int');
		$this->setState('sermons_category.id', $id);

		$id = JRequest::getVar('speaker_cat', 0, '', 'int');
		$this->setState('speakers_category.id', $id);

		$id = JRequest::getVar('series_cat', 0, '', 'int');
		$this->setState('series_category.id', $id);

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('speaker.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	function getSpeaker()
	{
		$speakerID = $this->getState('speaker.id');
		$database =& JFactory::getDBO();
		$query = "SELECT *, "
				."CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug "
				."FROM #__sermon_speakers WHERE id='".$speakerID."'";
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