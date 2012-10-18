<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * @package		SermonSpeaker
 */
// Based on com_contact
class SermonspeakerModelSerie extends JModelItem
{
	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= $app->input->get('id', 0, 'int');
		$this->setState('serie.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
		// Initialise variables.
		$id = (!empty($id)) ? $id : (int) $this->getState('serie.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$id])) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select(
					$this->getState(
						'item.select',
						'serie.id, serie.series_title, serie.series_description, serie.avatar, serie.catid, serie.metakey, serie.metadesc, '.
						'serie.checked_out, serie.checked_out_time, serie.language, '.
						'serie.hits, serie.state, serie.created, serie.created_by, serie.metakey, serie.metadesc, '.
						'CASE WHEN CHAR_LENGTH(serie.alias) THEN CONCAT_WS(\':\', serie.id, serie.alias) ELSE serie.id END as slug'
					)
				);
				$query->from('#__sermon_series AS serie');

				// Join on category table.
				$query->select('c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = serie.catid');
				$query->where('(serie.catid = 0 OR c.published = 1)');

				$query->where('serie.id = '.(int)$id);
				$query->where('serie.state = 1');

				// Join over users for the author names.
				$query->select("user.name AS author");
				$query->join('LEFT', '#__users AS user ON user.id = serie.created_by');

				$db->setQuery($query);

				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					throw new JException(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
				}

				$this->_item[$id] = $data;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$id] = false;
			}
		}

		return $this->_item[$id];
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

	function getSpeakers($series)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT sermons.speaker_id, speakers.name, speakers.pic, speakers.state, '
		. ' CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug'
        . ' FROM #__sermon_sermons AS sermons'
		. ' LEFT JOIN #__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id'
        . " WHERE sermons.state = '1'"
        . " AND sermons.speaker_id != '0'"
		. " AND sermons.series_id = '".$series."'"
        . ' GROUP BY sermons.speaker_id'
        . ' ORDER BY speakers.name';
		$db->setQuery($query);
		$speakers = $db->loadObjectList();

		return $speakers;
	}
}