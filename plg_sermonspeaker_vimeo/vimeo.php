<?php
/**
 * @package         SermonSpeaker
 * @subpackage      Plugin.SermonSpeaker
 * @author          Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright       © 2018 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JLoader::register('SermonspeakerPluginPlayer', JPATH_SITE . '/components/com_sermonspeaker/plugin/player.php');
JLoader::register('SermonspeakerHelperSermonspeaker', JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

/**
 * Plug-in to show the Vimeo videos
 *
 * @since  5.5.0
 */
class PlgSermonspeakerVimeo extends SermonspeakerPluginPlayer
{
	/**
	 * @var object  Holds the player object
	 */
	protected $player;

	/**
	 * @var boolean  True if scripts are loaded already
	 */
	private static $script_loaded = false;

	/**
	 * @var string player mode. Either 'audio' or 'video'.
	 */
	private $mode;

	/**
	 * @var string filetype mode. Either 'audio', 'video' or 'auto' (default).
	 */
	private $type;

	/**
	 * @var int which file to prioritise. Either 0 (audio) or 1 (video).
	 */
	private $fileprio;

	/**
	 * @var array Player options
	 */
	private $options;

	/**
	 * Creates the player
	 *
	 * @param   string                   $context The context from where it's triggered
	 * @param   object                   &$player Player object
	 * @param   array|object             $items   An array of sermnon objects or a single sermon object
	 * @param   Joomla\Registry\Registry $config  A config object. Special properties:
	 *                                            - count (id of the player)
	 *                                            - type (may be audio, video or auto)
	 *                                            - prio (may be 0 for audio or 1 for video)
	 *                                            - autostart (overwrites the backend setting)
	 *                                            - alt_player (overwrites the backend setting)
	 *                                            - awidth, aheight (width and height for audio)
	 *                                            - vwidth, vheight (width and height for video)
	 *
	 * @return  void
	 */
	public function onGetPlayer($context, &$player, $items, $config)
	{
		$this->player = $player;

		// There is already a player loaded
		if ($this->player->mspace)
		{
			return;
		}

		// Config asks for a specific player
		if ($config->get('alt_player', $this->_name) != $this->_name)
		{
			return;
		}

		if (is_array($items))
		{
			return;
		}

		// Merge $config into plugin params. $config takes priority.
		$this->params->merge($config);

		if ($this->params->get('type') === 'audio')
		{
			return;
		}

		$count = $this->params->get('count', 1);

		$supported = $this->isSupported($items);

		if (!$supported)
		{
			return;
		}

		$this->setDimensions(50, '100%');
		$this->setPopup('v');
		$id    = trim(strrchr($items->videofile, '/'), '/ ');
		$start = $this->params->get('autostart') ? 1 : 0;

		$this->player->player = $this->_name;
		$this->player->toggle = false;
		$this->player->mspace = '<iframe id="mediaspace' . $count . '" width="' . $this->config['vwidth'] . '" height="' . $this->config['vheight']
			. '" src="https://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0&border=0&autoplay=' . $start . '&player_id=vimeo'
			. $count . '&api=1"></iframe>';

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			if ($this->params->get('ga', ''))
			{
				JHtml::Script('media/plg_sermonspeaker_vimeo/js/ganalytics.js', true);
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration("window.addEvent('domready', _trackVimeo);");
			}

			self::$script_loaded = 1;
		}

		return;
	}

	/**
	 * Checks if either audio or videofile is supported
	 *
	 * @param   object $item Sermon object
	 *
	 * @return  array  supported files
	 */
	private function isSupported($item)
	{
		$supported = array();

		if (parse_url($item->videofile, PHP_URL_HOST) == 'vimeo.com'
			|| parse_url($item->videofile, PHP_URL_HOST) == 'player.vimeo.com'
		)
		{
			$supported[] = 'video';
		}

		return $supported;
	}
}
