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
class SermonspeakerModelspeakers extends JModelList
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
				'speakers.id, speakers.name, speakers.catid, speakers.pic, ' .
				'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug, ' .
				'speakers.hits, speakers.intro, speakers.bio, speakers.website, speakers.alias, ' .
				'speakers.state, speakers.ordering'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over Speakers Category.
		if ($categoryId = $this->getState('speakers_category.id')) {
			$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
			$query->where('speakers.catid = '.(int) $categoryId);
			$query->where('c_speaker.access IN ('.$groups.')');
		}

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('speakers.state = '.(int) $state);
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'speakers.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

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

		$id = (int)$params->get('speaker_cat', 0);
		if (!$id){ $id = JRequest::getInt('speaker_cat', 0); }
		$this->setState('speakers_category.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get the name of the category.
	 *
	 * @since	1.6
	 */
	public function getCat()
	{
		$categoryId = $this->getState('speakers_category.id');
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