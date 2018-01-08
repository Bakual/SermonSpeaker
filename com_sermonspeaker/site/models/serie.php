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
class SermonspeakerModelSerie extends JModelItem
{
	/**
	 * Method to auto-populate the model state
	 *
	 * Note. Calling getState in this method will result in recursion
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
		$this->setState('serie.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an object
	 *
	 * @param   int $id The id of the object to get
	 *
	 * @return mixed Object on success, false on failure
	 *
	 * @since ?
	 * @throws Exception
	 */
	public function &getItem($id = null)
	{
		$user = JFactory::getUser();

		// Initialise variables
		$id = ($id) ? $id : (int) $this->getState('serie.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$id]))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select(
				$this->getState(
					'item.select',
					'serie.id, serie.title, serie.series_description, serie.avatar, serie.catid, serie.metakey, serie.metadesc, '
					. 'serie.checked_out, serie.checked_out_time, serie.language, '
					. 'serie.hits, serie.state, serie.created, serie.created_by, serie.metakey, serie.metadesc, '
					. 'CASE WHEN CHAR_LENGTH(serie.alias) THEN CONCAT_WS(\':\', serie.id, serie.alias) ELSE serie.id END as slug, '
					. 'serie.publish_up, serie.publish_down'
				)
			);
			$query->from('#__sermon_series AS serie');

			// Filter by start and end dates.
			if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
			{
				$nullDate = $db->quote($db->getNullDate());
				$nowDate  = $db->quote(JFactory::getDate()->toSql());

				$query->where('(serie.publish_up = ' . $nullDate . ' OR serie.publish_up <= ' . $nowDate . ')');
				$query->where('(serie.publish_down = ' . $nullDate . ' OR serie.publish_down >= ' . $nowDate . ')');
			}

			// Join on category table
			$query->select('c.title AS category_title, c.access AS category_access');
			$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
			$query->join('LEFT', '#__categories AS c on c.id = serie.catid');
			$query->where('(serie.catid = 0 OR c.published = 1)');

			$query->where('serie.id = ' . (int) $id);
			$query->where('serie.state = 1');

			// Join over users for the author names
			$query->select("user.name AS author");
			$query->join('LEFT', '#__users AS user ON user.id = serie.created_by');

			$db->setQuery($query);

			try
			{
				$data = $db->loadObject();
			}
			catch (Exception $e)
			{
				$this->_item[$id] = false;

				throw new Exception($e->getMessage());
			}

			if (!$data)
			{
				throw new Exception(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			}

			$this->_item[$id] = $data;
		}

		return $this->_item[$id];
	}

	/**
	 * Method to increment the hit counter for the series
	 *
	 * @param   int $id Optional ID of the series
	 *
	 * @return  boolean  True on success
	 *
	 * @since ?
	 */
	public function hit($id = null)
	{
		if (!$id)
		{
			$id = $this->getState('serie.id');
		}

		$serie = $this->getTable('Serie', 'SermonspeakerTable');

		return $serie->hit($id);
	}

	/**
	 * Method to get speakers for a series
	 *
	 * @param   int $series Id of series
	 *
	 * @return  array
	 *
	 * @since ?
	 */
	public function getSpeakers($series)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(' DISTINCT sermons.speaker_id, speakers.title as speaker_title, speakers.pic, speakers.state');
		$query->select('speakers.intro, speakers.bio, speakers.website');
		$query->select('speakers.catid as speaker_catid, speakers.language as speaker_language');
		$query->select('CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug');
		$query->from('#__sermon_sermons AS sermons');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON sermons.speaker_id = speakers.id');
		$query->where('sermons.state = 1');
		$query->where('sermons.speaker_id != 0');
		$query->where('sermons.series_id = ' . (int) $series);
		$query->order('speakers.title');

		$db->setQuery($query);
		$speakers = $db->loadObjectList();

		return $speakers;
	}
}
