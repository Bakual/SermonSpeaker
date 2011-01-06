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
						'sermon.sermon_path, sermon.sermon_title, sermon.sermon_number, '.
						'sermon.sermon_scripture, sermon.sermon_date, sermon.sermon_date, '.
						'sermon.sermon_time, sermon.notes, sermon.state, '.
						'sermon.hits, sermon.addfile, sermon.addfileDesc, '.
						'sermon.metakey, sermon.metadesc, sermon.custom1, sermon.custom2'
					)
				);
				$query->from('#__sermon_sermons AS sermon');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = sermon.catid');

				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
				$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

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

	function getSerie($serie_id)
	{
		$database = &JFactory::getDBO();
		$query	= "SELECT id, series_title, \n"
				. "CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. "FROM #__sermon_series \n"
				. "WHERE id=".$serie_id;
		$database->setQuery($query);
		$series = $database->loadObject();
		
       return $series;
	}
	
	function getSpeaker($speaker_id)
	{
		$database = &JFactory::getDBO();
      	$query	= "SELECT id, name, pic, \n"
				. "CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug \n"
				. "FROM #__sermon_speakers \n"
				. "WHERE id=".$speaker_id;
		$database->setQuery($query);
      	$speaker = $database->loadObject();
		
       return $speaker;
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
		if (empty($id)) {
			$id = $this->getState('sermon.id');
		}

		$sermon = $this->getTable('Sermon', 'SermonspeakerTable');
		return $sermon->hit($id);
	}
}