<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JLoader::register('SermonspeakerPluginPlayer', JPATH_SITE . '/components/com_sermonspeaker/plugin/player.php');
/**
 * Plug-in to call a 3rd party plugin to show the player
 *
 * @since  5.3.0
 */
class PlgSermonspeakerGeneric extends SermonspeakerPluginPlayer
{
	/**
	 * Creates the player
	 *
	 * @param   string        $context  The context from where it's triggered
	 * @param   array/object  $items    An array of sermnon objects or a single sermon object
	 * @param   array         $config   Should be an array of config options. Valid options:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 * @param   boolean       &$loaded  Set to true if another player is already loaded
	 *
	 * @return  object|false  The player object or false
	 */
	public function onGetPlayer($context, $items, $config, &$loaded)
	{
		// There is already a player loaded
		if ($loaded)
		{
			return false;
		}

		// Config asks for a specific player
		if (isset($config['alt_player']) && ($config['alt_player'] != 'generic'))
		{
			return false;
		}

		$start = $this->params->get('tag_start');
		$end   = $this->params->get('tag_end');
		$mode  = $this->params->get('mode');

		if (!$start && !$end)
		{
			return false;
		}

		if (is_array($items) && !$this->params->get('multiple'))
		{
			// Playlist not supported by plugin, take first item
			$items = $items[0];
		}

		if (is_array($items))
		{
			$separator = $this->params->get('multiple_separator');
			$files     = array();

			foreach ($items as $item)
			{
				$files = ($mode) ? $item->videofile : $item->audiofile;
			}

			$file = implode($separator, $files);
		}
		else
		{
			$file = ($mode) ? $items->videofile : $items->audiofile;
		}

		$content = $start . $file . $end;

		$this->player->mspace = JHtml::_('content.prepare', $content);
		$loaded = true;

		return $this->player;
	}
}
