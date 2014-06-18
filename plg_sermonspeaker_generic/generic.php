<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Plug-in to call a 3rd party plugin to show the player
 *
 * @since  5.3.0
 */
class PlgSermonspeakerGeneric extends JPlugin
{
	/**
	 * Plugin that shows a SermonSpeaker player
	 *
	 * @param   array/object  $items  An array of objects or a single object
	 *
	 * @return  string  The output needed to load the player
	 */
	public function onPlayerInsert($items)
	{
		$start = $this->params->get('tag_start');
		$end   = $this->params->get('tag_end');
		$mode  = $this->params->get('mode');

		if (is_array($item) && !$this->params->get('multiple'))
		{
			// playlists not supported by plugin, take first item
			$item = $item[0];
		}

		if (is_array($item))
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
			$file = ($mode) ? $item->videofile : $item->audiofile;
		}

		return $start . $file . $end;
	}
}
