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
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'ordering', 'speakers.ordering',
				'name', 'speakers.name',
				'hits', 'speakers.hits',
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
				'speakers.id, speakers.name, speakers.catid, speakers.pic, ' .
				'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug, ' .
				'speakers.hits, speakers.intro, speakers.bio, speakers.website, speakers.alias, ' .
				'speakers.state, speakers.ordering, speakers.created, speakers.created_by'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		if ($categoryId = $this->getState('speakers_category.id')) {
			$query->where('speakers.catid = '.(int) $categoryId);
		}
		$query->where('(speakers.catid = 0 OR (c_speaker.access IN ('.$groups.') AND c_speaker.published = 1))');

		// Subquerie to get counts of sermons and series
		$query->select('(SELECT COUNT(DISTINCT sermons.id) FROM #__sermon_sermons AS sermons WHERE sermons.speaker_id = speakers.id AND sermons.id > 0) AS sermons');
		$query->select('(SELECT COUNT(DISTINCT sermons2.series_id) FROM #__sermon_sermons AS sermons2 WHERE sermons2.speaker_id = speakers.id AND sermons2.series_id > 0) AS series');

		// Grouping by speaker
		$query->group('speakers.id');

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = speakers.created_by');

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('speakers.state = '.(int) $state);
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

		$id = (int)$params->get('speaker_cat', 0);
		if (!$id){ $id = JRequest::getInt('speaker_cat', 0); }
		$this->setState('speakers_category.id', $id);

		$this->setState('filter.state',	1);

		parent::populateState('ordering', 'ASC');
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