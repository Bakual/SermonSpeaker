<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Churchtool
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Plug-in to prepopulate the sermon form from the Churchtool software
 *
 * @since  1.0.0
 */
class PlgContentChurchtoolsermonspeaker extends JPlugin
{
	/**
	 * Runs on content preparation
	 *
	 * @param   string  $context  The context for the data
	 * @param   object  $data     An object containing the data for the form.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onContentPrepareData($context, $data)
	{
		// Only act for SermonSpeaker sermons
		if ($context != 'com_sermonspeaker.sermon')
		{
			return;
		}

		// Only act on new sermons
		if (!empty($data->id))
		{
			return;
		}

		// Setting up custom logger
		JLog::addLogger(
			array('text_file' => 'sermonspeaker.php'),
			JLog::ALL,
			array('plg_content_churchtoolsermonspeaker')
		);

		// Taken from https://docs.joomla.org/Connecting_to_an_external_database
		$option = array();

		$option['driver']   = $this->params->get('db_type', 'mysqli');
		$option['host']     = $this->params->get('db_host', 'localhost');
		$option['database'] = $this->params->get('db_database');
		$option['user']     = $this->params->get('db_user');
		$option['password'] = $this->params->get('db_pass');
		$option['prefix']   = '';

		$db = JDatabaseDriver::getInstance($option);

		// Sanitise Eventgroups
		$eventgroups = $this->params->get('eventgroups');
		$eventgroups = Joomla\Utilities\ArrayHelper::toInteger($eventgroups);

		// Build Query for events
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'title', 'kommentar', 'kommentar2')));
		$query->from($db->quoteName('ko_event'));
		$query->where($db->quoteName('startdatum') . ' = CURDATE()');
		$query->where($db->quoteName('startzeit') . ' < CURTIME()');
		$query->where($db->quoteName('rota') . ' = 1');
		$query->where($db->quoteName('eventgruppen_id') . ' IN (' . implode(',', $eventgroups) . ')');
		$query->order($db->quoteName('startzeit') . ' DESC');

		$db->setQuery($query, 0, 1);

		try
		{
			$event = $db->loadObject();
		}
		catch (Exception $e)
		{
			JLog::add($e->getMessage(), JLog::WARNING, 'plg_content_churchtoolsermonspeaker');

			return;
		}

		if (!$event)
		{
			return;
		}

		// Build Query for the speaker
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('leute.nachname', 'leute.vorname')));
		$query->from($db->quoteName('ko_leute', 'leute'));
		$query->join('INNER', $db->quoteName('ko_rota_schedulling', 'rota')
			. ' ON FIND_IN_SET(' . $db->quoteName('leute.id') . ', ' . $db->quoteName('rota.schedule') . ')');
		$query->where($db->quoteName('rota.event_id') . ' = ' . (int) $event->id);
		$query->where($db->quoteName('rota.team_id') . ' = ' . (int) $this->params->get('team_id'));

		$db->setQuery($query);

		try
		{
			$speakers = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JLog::add($e->getMessage(), JLog::WARNING, 'plg_content_churchtoolsermonspeaker');

			return;
		}

		if (!$speakers)
		{
			return;
		}

		$speaker_array = array();

		foreach ($speakers as $speaker)
		{
			$speaker_array[] = $speaker->vorname . ' ' . $speaker->nachname;
		}

		// Fetch default Joomla Databasedriver
		$dbo = JFactory::getDbo();

		// Build Query to get id of speaker. Take first one found.
		$query = $dbo->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__sermon_speakers'));

		foreach ($speakers as $speaker)
		{
			$query->where($db->quoteName('title') . ' = ' . $db->quote($speaker->vorname . ' ' . $speaker->nachname), 'OR');
		}

		$dbo->setQuery($query, 0, 1);

		$id = $dbo->loadResult();

		$data->speaker_id = $id;

		switch ($this->params->get('title_field', 1))
		{
			case 0:
				$title = $event->title;
				break;
			case 1:
			default:
				$title = $event->kommentar;
				break;
			case 2:
				$title = $event->kommentar2;
				break;
		}

		$data->title = $title;
	}
}
