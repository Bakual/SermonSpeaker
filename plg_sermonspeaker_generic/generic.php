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
	 * Plugin that shows a SermonSpeaker player
	 *
	 * @param   array/object  $items  An array of objects or a single object
	 *
	 * @return  object  The player object
	 */
	public function onGetPlayer($context, $items, $config, &$loaded)
	{
		// There is already a player loaded
		if ($loaded)
		{
			return $this->player;
		}

		$start = $this->params->get('tag_start');
		$end   = $this->params->get('tag_end');
		$mode  = $this->params->get('mode');

		if (!$start && !$end)
		{
			$this->player->error = 'No tags set';

			return $this->player;
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
