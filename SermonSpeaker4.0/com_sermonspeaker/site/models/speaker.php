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
class SermonspeakerModelSpeaker extends JModelItem
{
	protected $_context = 'com_sermonspeaker.speaker';

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
		$this->setState('speaker.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
		// Initialise variables.
		$id = (!empty($id)) ? $id : (int) $this->getState('speaker.id');

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
						'speaker.id, speaker.name, speaker.alias, speaker.website, speaker.state,'.
						'CASE WHEN CHAR_LENGTH(speaker.alias) THEN CONCAT_WS(\':\', speaker.id, speaker.alias) ELSE speaker.id END as slug,'.
						'speaker.checked_out, speaker.checked_out_time, speaker.language,'.
						'speaker.intro, speaker.bio, speaker.pic, speaker.hits, speaker.created, speaker.created_by,'.
						'speaker.metakey, speaker.metadesc, speaker.created, speaker.created_by'
					)
				);
				$query->from('#__sermon_speakers AS speaker');

				// Join on category table.
				$query->select('c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = speaker.catid');
				$query->where('(speaker.catid = 0 OR c.published = 1)');

				$query->where('speaker.id = '.(int)$id);
				$query->where('speaker.state = 1');

				// Join over users for the author names.
				$query->select("user.name AS author");
				$query->join('LEFT', '#__users AS user ON user.id = speaker.created_by');

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