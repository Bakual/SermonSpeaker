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
 * Plug-in to prepopulate the sermon form from the churchtool software
 *
 * @since  1.0.0
 */
class PlgSermonspeakerChurchtool extends JPlugin
{
	/**
	 * Creates the player
	 *
	 * @param   string  $context  The context from where it's triggered
	 * @param   object  $data     Player object
	 *
	 * @return  void
	 */
	public function onContentPrepareData($context, $data)
	{
		echo 'test';

	}
}
