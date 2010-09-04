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
	public function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= JRequest::getInt('id');
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
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('sermon.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Sermon', 'SermonspeakerTable');

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$this->_item = JArrayHelper::toObject($table->getProperties(1), 'JObject');
			}
			else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}

	/**
	 * Method to increment the hit counter for the weblink
	 *
	 * @param	int		Optional ID of the weblink.
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function hit($id = null)
	{
		if (empty($id)) {
			$id = $this->getState('sermon.id');
		}

		$sermon = $this->getTable('Sermon', 'SermonspeakerTable');
		return $sermon->hit($id);
	}
}