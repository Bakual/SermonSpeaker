<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSermon extends JModelItem
{
	protected $_context = 'com_sermonspeaker.sermon';

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
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var \Joomla\CMS\Application\SiteApplication $app */
		$app    = Factory::getApplication();
		$params = $app->getParams();

		// Load the object state.
		$id = $app->input->get('id', 0, 'int');
		$this->setState('sermon.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get an object
	 *
	 * @param   integer $id The id of the object to get
	 *
	 * @return mixed Object on success, false on failure
	 *
	 * @since ?
	 * @throws Exception
	 */
	public function &getItem($id = null)
	{
		$user = JFactory::getUser();

		// Initialise variables.
		$id = ($id) ? $id : (int) $this->getState('sermon.id');

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
					'sermon.id, sermon.speaker_id, sermon.series_id, sermon.alias, sermon.catid, '
					. 'CASE WHEN CHAR_LENGTH(sermon.alias) THEN CONCAT_WS(\':\', sermon.id, sermon.alias) ELSE sermon.id END as slug,'
					. 'sermon.audiofile, sermon.videofile, sermon.title, sermon.sermon_number, '
					. 'sermon.sermon_date, sermon.picture, sermon.checked_out, sermon.checked_out_time, '
					. 'sermon.sermon_time, sermon.notes, sermon.state, sermon.language, '
					. 'sermon.hits, sermon.addfile, sermon.addfileDesc, '
					. 'sermon.metakey, sermon.metadesc, '
					. 'sermon.created, sermon.created_by, sermon.audiofilesize, sermon.videofilesize, '
					. 'sermon.metadata, '
					. 'sermon.publish_up, sermon.publish_down'
				)
			);
			$query->from('#__sermon_sermons AS sermon');

			// Join over users for the author names.
			$query->select("user.name AS author");
			$query->join('LEFT', '#__users AS user ON user.id = sermon.created_by');

			// Filter by start and end dates.
			if ((!$user->authorise('core.edit.state', 'com_sermonspeaker')) && (!$user->authorise('core.edit', 'com_sermonspeaker')))
			{
				$nullDate = $db->quote($db->getNullDate());
				$nowDate  = $db->quote(JFactory::getDate()->toSql());

				$query->where('(sermon.publish_up = ' . $nullDate . ' OR sermon.publish_up <= ' . $nowDate . ')');
				$query->where('(sermon.publish_down = ' . $nullDate . ' OR sermon.publish_down >= ' . $nowDate . ')');
			}

			// Join on category table.
			$query->select('c.title AS category_title, c.access AS category_access');
			$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
			$query->join('LEFT', '#__categories AS c on c.id = sermon.catid');

			// Join on speakers table.
			$query->select('speakers.title AS speaker_title, speakers.pic AS pic, speakers.state as speaker_state');
			$query->select('speakers.intro, speakers.bio, speakers.website, speakers.catid as speaker_catid, speakers.language as speaker_language');
			$query->select("CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(':', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug");
			$query->join('LEFT', '#__sermon_speakers AS speakers on speakers.id = sermon.speaker_id');

			// Join on category table for speaker
			$query->select('c_speaker.access AS speaker_category_access');
			$query->join('LEFT', '#__categories AS c_speaker on c_speaker.id = speakers.catid');
			$query->where('(sermon.speaker_id = 0 OR speakers.catid = 0 OR c_speaker.published = 1)');

			// Join on series table.
			$query->select('series.title AS series_title, series.avatar, series.state as series_state, series.catid as series_catid, series.language as series_language');
			$query->select("CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(':', series.id, series.alias) ELSE series.id END as series_slug ");
			$query->join('LEFT', '#__sermon_series AS series on series.id = sermon.series_id');

			// Join on category table for series
			$query->select('c_series.access AS series_category_access');
			$query->join('LEFT', '#__categories AS c_series on c_series.id = series.catid');
			$query->where('(sermon.series_id = 0 OR series.catid = 0 OR c_series.published = 1)');

			$query->where('sermon.id = ' . (int) $id);
			$query->where('sermon.state > 0');

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

			// Query Scripture.
			$scriptureQuery = $db->getQuery(true);
			$scriptureQuery->select('GROUP_CONCAT(book,"|",cap1,"|",vers1,"|",cap2,"|",vers2,"|",text '
				. 'ORDER BY ordering ASC SEPARATOR "!") AS scripture');
			$scriptureQuery->from('#__sermon_scriptures');
			$scriptureQuery->where('sermon_id = ' . $data->id);
			$scriptureQuery->group('sermon_id');

			$db->setQuery($scriptureQuery);
			$data->scripture = $db->loadResult();

			// Convert the metadata field to an array.
			$registry = new Joomla\Registry\Registry;
			$registry->loadString($data->metadata);
			$data->metadata = $registry;

			$this->_item[$id] = $data;
		}

		return $this->_item[$id];
	}

	/**
	 * Method to increment the hit counter for the sermon
	 *
	 * @param   int $id Optional ID of the sermon
	 *
	 * @return  boolean  True on success
	 *
	 * @since ?
	 * @throws Exception
	 */
	public function hit($id = null)
	{
		if (!$id)
		{
			$id = $this->getState('sermon.id');
		}

		$sermon = $this->getTable('Sermon', 'SermonspeakerTable');

		return $sermon->hit($id);
	}

	/**
	 * Method to get the latest sermon
	 *
	 * @return  object  sermon object
	 *
	 * @since ?
	 */
	public function getLatest()
	{
		$levels = JFactory::getUser()->getAuthorisedViewLevels();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);

		$query->select('a.id');
		$query->from('#__sermon_sermons AS a');
		$query->where('a.state = 1');
		$query->join('left', '#__categories AS c ON a.catid = c.id');
		$query->where('c.published = 1');
		$query->where('c.access IN (' . implode(',', $levels) . ')');

		// Filter by filetype
		$filetype = $this->getState('params')->get('filetype', '');

		if ($filetype == 'video')
		{
			$query->where('a.videofile != ""');
		}
		elseif ($filetype == 'audio')
		{
			$query->where('a.audiofile != ""');
		}

		$query->order('a.sermon_date DESC');

		$db->setQuery($query);

		return $db->loadResult();
	}
}
