<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Plugin\Sermonspeaker\Generic\Extension;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper;
use Sermonspeaker\Component\Sermonspeaker\Site\Plugin\Player;

defined('_JEXEC') or die();

/**
 * Plug-in to call a 3rd party plugin to show the player
 *
 * @since  5.3.0
 */
class Generic extends Player
{
	/**
	 * Creates the player
	 *
	 * @param   string                    $context  The context from where it's triggered
	 * @param   object                    &$player  Player object
	 * @param   array|object              $items    An array of sermnon objects or a single sermon object
	 * @param   Registry  $config   A config object. Special properties:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 *
	 * @return  void
	 */
	public function onGetPlayer($context, $player, $items, $config): void
	{
		// There is already a player loaded
		if ($player->mspace)
		{
			return;
		}

		// Config asks for a specific player
		if ($config->get('alt_player', $this->_name) != $this->_name)
		{
			return;
		}

		// Merge $config into plugin params. $config takes priority.
		$this->params->merge($config);

		$start = $this->params->get('tag_start');
		$end   = $this->params->get('tag_end');
		$mode  = $this->params->get('mode');

		if (!$start && !$end)
		{
			return;
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

		$file = SermonspeakerHelper::makeLink($file);

		$content = $start . $file . $end;

		$player->player = $this->_name;
		$player->mspace = HTMLHelper::_('content.prepare', $content);
	}
}
