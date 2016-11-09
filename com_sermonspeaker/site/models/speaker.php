<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSpeaker extends JModelItem
{
	protected $_context = 'com_sermonspeaker.speaker';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  Ordering column
	 * @param   string $direction 'ASC' or 'DESC'
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var JApplicationSite $app */
		$app    = JFactory::getApplication();
		$params = $app->getParams();

		// Load the object state.
		$id = $app->input->get('id', 0, 'int');
		$this->setState('speaker.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   int $id The id of the object to get.
	 *
	 * @return mixed Object on success, false on failure.
	 *
	 * @since ?
	 */
	public function &getItem($id = null)
	{
		$user = JFactory::getUser();

		// Initialise variables.
		$id = ($id) ? $id : (int) $this->getState('speaker.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$id]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select(
					$this->getState(
						'item.select',
						'speaker.id, speaker.title, speaker.alias, speaker.website, speaker.state, speaker.catid,'
						. 'CASE WHEN CHAR_LENGTH(speaker.alias) THEN CONCAT_WS(\':\', speaker.id, speaker.alias) ELSE speaker.id END as slug,'
						. 'speaker.checked_out, speaker.checked_out_time, speaker.language,'
						. 'speaker.intro, speaker.bio, speaker.pic, speaker.hits, speaker.created, speaker.created_by,'
						. 'speaker.metakey, speaker.metadesc, speaker.created, speaker.created_by,'
						. 'speaker.publish_up, speaker.publish_down'
					)
				);
				$query->from('#__sermon_speakers AS speaker');

				// Filter by start and end dates.
				if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
				{
					$nullDate = $db->quote($db->getNullDate());
					$nowDate  = $db->quote(JFactory::getDate()->toSql());

					$query->where('(speaker.publish_up = ' . $nullDate . ' OR speaker.publish_up <= ' . $nowDate . ')');
					$query->where('(speaker.publish_down = ' . $nullDate . ' OR speaker.publish_down >= ' . $nowDate . ')');
				}

				// Join on category table.
				$query->select('c.title AS category_title, c.access AS category_access');
				$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
				$query->join('LEFT', '#__categories AS c on c.id = speaker.catid');
				$query->where('(speaker.catid = 0 OR c.published = 1)');

				$query->where('speaker.id = ' . (int) $id);
				$query->where('speaker.state = 1');

				// Join over users for the author names.
				$query->select("user.name AS author");
				$query->join('LEFT', '#__users AS user ON user.id = speaker.created_by');

				$db->setQuery($query);

				$data = $db->loadObject();

				if ($error = $db->getErrorMsg())
				{
					throw new Exception($error);
				}

				if (!$data)
				{
					throw new Exception(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
				}

				$this->_item[$id] = $data;
			}
			catch (Exception $e)
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
	 * @param   int $id Optional ID of the speaker.
	 *
	 * @return  boolean  True on success
	 *
	 * @since ?
	 */
	public function hit($id = null)
	{
		if (!$id)
		{
			$id = $this->getState('speaker.id');
		}

		$speaker = $this->getTable('Speaker', 'SermonspeakerTable');

		return $speaker->hit($id);
	}
}
