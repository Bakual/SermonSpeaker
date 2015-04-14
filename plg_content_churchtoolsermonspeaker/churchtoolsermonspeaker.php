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

		
	}
}
