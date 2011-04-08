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
				'sermons.id, sermons.sermon_title, sermons.catid, sermons.audiofile, sermons.videofile, ' .
				'CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug,' .
				'sermons.hits, sermons.picture, sermons.notes, sermons.sermon_scripture,' .
				'sermons.sermon_date, sermons.alias, sermons.sermon_time,' .
				'sermons.state, sermons.ordering, sermons.podcast,' .
				'sermons.sermon_number, sermons.addfile, sermons.addfileDesc'
			)
		);
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over Speaker
		$query->select(
			'speakers.name AS name, speakers.pic AS pic, speakers.state as speaker_state, ' .
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

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		if ($categoryId = $this->getState('speakers_category.id')) {
			$query->where('speakers.catid = '.(int) $categoryId);
		}
		$query->where('(sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN ('.$groups.') AND c_speaker.published = 1))');

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
		$params	= $app->getParams();

		// List state information
		$search = JRequest::getString('filter-search', '');
		$this->setState('filter.search', $search);

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', $params->get('default_order', 'ordering'));
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $params->get('default_order_dir', 'ASC'));
		$this->setState('list.direction', $listOrder);

		$id = (int)$params->get('sermon_cat', 0);
		if (!$id){ $id = JRequest::getInt('sermon_cat', 0); }
		$this->setState('sermons_category.id', $id);

		$id = (int)$params->get('speaker_cat', 0);
		if (!$id){ $id = JRequest::getInt('speaker_cat', 0); }
		$this->setState('speakers_category.id', $id);

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setState('serie.id', $id);

		$this->setState('filter.state',	1);

		// Load the parameters.
		$this->setState('params', $params);
	}
	
	function getSerie()
	{
		$database =& JFactory::getDBO();
		$query 	= "SELECT *, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. "FROM #__sermon_series \n"
				. "WHERE id='".$this->getState('serie.id')."'";
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

	/**
	 * Method to get the name of the category.
	 *
	 * @since	1.6
	 */
	public function getCat()
	{
		$cat_arr = array();
		if($categoryId = $this->getState('sermons_category.id')){
			$cat_arr[] = $categoryId;
		}
		if($categoryId = $this->getState('series_category.id')){
			$cat_arr[] = $categoryId;
		}
		if($categoryId = $this->getState('speakers_category.id')){
			$cat_arr[] = $categoryId;
		}
		$cat_arr 	= array_unique($cat_arr);
		$db		= $this->getDbo();
		$title = array();
		foreach ($cat_arr as $cat){
			$query = "SELECT title FROM #__categories WHERE id = ".$cat;
			$db->setQuery( $query );
			$title[] = $db->LoadResult();
		}
		$title = implode(' &amp; ', $title);
		return $title;
	}
}