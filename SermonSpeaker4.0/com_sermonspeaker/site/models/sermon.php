<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modelitem');

/**
 * Sermonspeaker Component Model for a sermon record
 *
 * @package		Sermonspeaker
 */
class SermonspeakerModelSermon extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_sermonspeaker.sermon';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= $app->input->get('id', 0, 'int');
		$this->setState('sermon.id', $id);

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
		$id = (!empty($id)) ? $id : (int) $this->getState('sermon.id');

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
						'sermon.id, sermon.speaker_id, sermon.series_id, sermon.alias, '.
						'CASE WHEN CHAR_LENGTH(sermon.alias) THEN CONCAT_WS(\':\', sermon.id, sermon.alias) ELSE sermon.id END as slug,' .
						'sermon.audiofile, sermon.videofile, sermon.sermon_title, sermon.sermon_number, '.
						'sermon.sermon_date, sermon.picture, sermon.checked_out, sermon.checked_out_time, '.
						'sermon.sermon_time, sermon.notes, sermon.state, sermon.language, '.
						'sermon.hits, sermon.addfile, sermon.addfileDesc, '.
						'sermon.metakey, sermon.metadesc, sermon.custom1, sermon.custom2, '.
						'sermon.created, sermon.created_by'
					)
				);
				$query->from('#__sermon_sermons AS sermon');

				// Join over the scriptures.
				$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
				$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = sermon.id');
				$query->group('sermon.id');

				// Join over users for the author names.
				$query->select("user.name AS author");
				$query->join('LEFT', '#__users AS user ON user.id = sermon.created_by');

				// Join on category table.
				$query->select('c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = sermon.catid');

				// Join on speakers table.
				$query->select(
						'speakers.name AS name, speakers.pic AS pic, speakers.state as speaker_state, '.
						"CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(':', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug "
				);
				$query->join('LEFT', '#__sermon_speakers AS speakers on speakers.id = sermon.speaker_id');

				// Join on category table for speaker
				$query->select('c_speaker.access AS speaker_category_access');
				$query->join('LEFT', '#__categories AS c_speaker on c_speaker.id = speakers.catid');
				$query->where('(sermon.speaker_id = 0 OR speakers.catid = 0 OR c_speaker.published = 1)');

				// Join on series table.
				$query->select(
						'series.series_title, series.avatar, series.state as series_state, '.
						"CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(':', series.id, series.alias) ELSE series.id END as series_slug "
				);
				$query->join('LEFT', '#__sermon_series AS series on series.id = sermon.series_id');

				// Join on category table for series
				$query->select('c_series.access AS series_category_access');
				$query->join('LEFT', '#__categories AS c_series on c_series.id = series.catid');
				$query->where('(sermon.series_id = 0 OR series.catid = 0 OR c_series.published = 1)');

				$query->where('sermon.id = '.(int)$id);
				$query->where('sermon.state = 1');

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
	 * Method to increment the hit counter for the sermon
	 *
	 * @param	int		Optional ID of the sermon.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function hit($id = null)
	{
		if (empty($id))
		{
			$id = $this->getState('sermon.id');
		}

		$sermon = $this->getTable('Sermon', 'SermonspeakerTable');
		return $sermon->hit($id);
	}

	/**
	 * Method to get the tags for the sermon
	 *
	 * @param	int		Optional ID of the sermon.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function getTags($id = null)
	{
		if (empty($id))
		{
			$id = $this->getState('sermon.id');
		}

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('tags.title');
		$query->from('#__sermon_sermons_tags AS st');
		$query->join('LEFT', '#__sermon_tags AS tags ON st.tag_id = tags.id');
		$query->where('st.sermon_id = '.$id);
		$query->where('tags.state = 1');
		$query->order('tags.title ASC');

		$db->setQuery($query);

		$tags = $db->loadResultArray();
		return $tags;
	}

	/**
	 * Method to get the tags for the sermon
	 *
	 * @param	int		Optional ID of the sermon.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function getLatest()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__sermon_sermons');
		$query->order('sermon_date DESC');

		$db->setQuery($query, 0, 1);

		$id = $db->loadResult();
		return $id;
	}
}