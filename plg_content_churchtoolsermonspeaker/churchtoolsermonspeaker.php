<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
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
		if ($data->id)
		{
			return;
		}

		// Taken from https://docs.joomla.org/Connecting_to_an_external_database
		$option = array();

		$option['driver']   = $this->params->get('db_type', 'mysqli');
		$option['host']     = $this->params->get('db_host', 'localhost');
		$option['database'] = $this->params->get('db_database');
		$option['user']     = $this->params->get('db_user');
		$option['password'] = $this->params->get('db_pass');
		$option['prefix']   = '';

		$db = JDatabaseDriver::getInstance($option);

		$date = JFactory::getDate();

		// Sanitise Eventgroups
		$eventgroups = $this->params->get('eventgroups');
		$eg_array    = explode(',', $eventgroups);
		$eg_array    = Joomla\Utilities\ArrayHelper::toInteger($eg_array);
		$eventgroups = implode(',', $eg_array);

		// Build Query for events
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'startzeit', 'kommentar')));
		$query->from($db->quoteName('ko_event'));
//		$query->where($db->quoteName('startdatum') . ' = ' . $db->quote($date->format('Y-m-d')));
		$query->where($db->quoteName('startdatum') . ' = ' . $db->quote('2015-04-12'));
		$query->where($db->quoteName('rota') . ' = 1');
		$query->where($db->quoteName('eventgruppen_id') . ' IN (' . $eventgroups . ')');
		$query->order($db->quoteName('startzeit') . ' ASC');

		$db->setQuery($query);

		try
		{
			$events = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return;
		}
dump($events, 'events');
		if (!$events)
		{
			return;
		}

		if (count($events) > 1)
		{
			// Current time without colons
			$time = $date->format('Hms');

			// Get latest past event
			foreach ($events as $key => $value)
			{
				$starttime = (int) $value->startzeit;

				if ($starttime > $time)
				{
					break;
				}

				$event = $value;
			}
		}
		else
		{
			$event = $events[0];
		}
dump($event, 'event');
		// Build Query for rota
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('rota.schedule')));
		$query->from($db->quoteName('ko_rota_schedulling'));
		$query->where($db->quoteName('event_id') . ' = ' . (int) $event->id);
		$query->where($db->quoteName('team_id') . ' = ' . (int) $this->params->get('team_id'));

		$db->setQuery($query);

		try
		{
			$schedules = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return;
		}
dump($schedules, 'schedules');
		// "find_in_set" may help
		$subquery = $db->getQuery(true);
		$subquery->select($db->quoteName('rota.schedule'));
		$subquery->from($db->quoteName('ko_rota_schedulling'));
		$subquery->where($db->quoteName('event_id') . ' = ' . (int) $event->id);
		$subquery->where($db->quoteName('team_id') . ' = ' . (int) $this->params->get('team_id'));

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('leute.nachname', 'leute.vorname')));
		$query->from($db->quoteName('ko_leute', 'leute'));
		$query->where($db->quoteName('leute.id') . ' IN (' . $subquery . ')');

		$db->setQuery($query);

		try
		{
			$schedules = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return;
		}
		dump($schedules, 'schedules');



	}
}
