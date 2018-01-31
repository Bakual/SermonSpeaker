<?php
/**
 * @package         SermonSpeaker
 * @subpackage      Plugin.SermonSpeaker
 * @author          Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JLoader::register('SermonspeakerPluginPlayer', JPATH_SITE . '/components/com_sermonspeaker/plugin/player.php');
JLoader::register('SermonspeakerHelperSermonspeaker', JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

/**
 * Plug-in to show the MediaElement from http://www.mediaelementjs.com/
 *
 * @since  1.0.0
 */
class PlgSermonspeakerMediaelement extends SermonspeakerPluginPlayer
{
	/**
	 * @var object  Holds the player object
	 *
	 * @since  1.0.0
	 */
	protected $player;

	/**
	 * @var boolean  True if scripts are loaded already
	 *
	 * @since  1.0.0
	 */
	private static $script_loaded = false;

	/**
	 * @var string player mode. Either 'audio' or 'video'.
	 *
	 * @since  1.0.0
	 */
	private $mode;

	/**
	 * @var string filetype mode. Either 'audio', 'video' or 'auto' (default).
	 *
	 * @since  1.0.0
	 */
	private $type;

	/**
	 * @var int which file to prioritise. Either 0 (audio) or 1 (video).
	 *
	 * @since  1.0.0
	 */
	private $fileprio;

	/**
	 * @var array Player options
	 *
	 * @since  1.0.0
	 */
	private $options;

	/**
	 * @var Joomla\Registry\Registry Component Parameters
	 *
	 * @since  1.0.0
	 */
	protected $c_params;

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
	 *
	 * @since  1.0.0
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

		// Merge $config into plugin params. $config takes priority.
		$this->params->merge($config);

		// Get component params
		$this->c_params = JComponentHelper::getParams('com_sermonspeaker');

		$this->fileprio = $this->params->get('fileprio');
		$this->type     = $this->params->get('type', 'auto');
		$count          = $this->params->get('count', 1);
		$toggle         = $this->params->get('filetoggle');

		// Detect mode and which filetype to use
		if (is_array($items))
		{
			$this->mode = ($this->type == 'audio' || ($this->type == 'auto' && !$this->fileprio)) ? 'audio' : 'video';
		}
		else
		{
			$supported = $this->isSupported($items);

			if (!$supported)
			{
				return;
			}

			if ($this->type != 'auto')
			{
				if (!in_array($this->type, $supported))
				{
					return;
				}

				$this->mode = $this->type;
			}
			else
			{
				if (count($supported) == 1)
				{
					// Only one file is supported
					$this->mode = $supported[0];
					$toggle     = false;
				}
				else
				{
					// Both files are supported
					$this->mode = $supported[$this->fileprio];
				}
			}
		}

		$player->player = $this->_name;

		// Set width and height for later use
		$dimensions['audiowidth']  = $this->params->get('awidth', '100%');
		$dimensions['audioheight'] = $this->params->get('aheight', '40');
		$dimensions['videowidth']  = $this->params->get('vwidth', '100%');
		$dimensions['videoheight'] = $this->params->get('vheight', '300');

		$autoplay = $this->params->get('autostart') ? ' autoplay="autoplay"' : '';
		$file = $this->mode . 'file';

		$player->mspace = '<' . $this->mode . ' " class="mejs__player"' . $autoplay . ' controls="controls"'
			. ' width="' . $dimensions[$this->mode . 'width'] . '" height="' . $dimensions[$this->mode . 'height'] . '"'
			. ' src="' . SermonspeakerHelperSermonspeaker::makeLink($items->$file) . '"'
			. ' data-mejsoptions=\'{"alwaysShowControls": "true"}\'>'
			. '</' . $this->mode . '>';
		$player->toggle = $toggle;
		$this->loadLanguage();

		$this->setPopup($this->mode[0]);

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/mediaelement-and-player.min.js', false, true, false);
			JHtml::_('stylesheet', 'plg_sermonspeaker_mediaelement/mediaelementplayer.min.css', false, true, false);

//			JFactory::getDocument()->addScriptDeclaration("function ss_play(id){jwplayer('mediaspace" . $count . "').playlistItem(id);}");

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
	 *
	 * @since  1.0.0
	 */
	private function isSupported($item)
	{
		$supported = array();

		if (!$item->audiofile && !$item->videofile)
		{
			return $supported;
		}

		// Define supported file extensions
		$audio_ext = array('aac', 'm4a', 'f4a', 'mp3', 'ogg', 'oga');
		$video_ext = array('mp4', 'm4v', 'f4v', 'mov', 'flv', 'webm');

		if (in_array(JFile::getExt(strtok($item->audiofile, '?')), $audio_ext))
		{
			$supported[] = 'audio';
		}

		if (in_array(JFile::getExt(strtok($item->videofile, '?')), $video_ext))
		{
			$supported[] = 'video';
		}

		if (parse_url($item->videofile, PHP_URL_HOST) == 'youtube.com'
			|| parse_url($item->videofile, PHP_URL_HOST) == 'www.youtube.com'
			|| parse_url($item->videofile, PHP_URL_HOST) == 'youtu.be'
		)
		{
			$supported[] = 'video';
		}

		return $supported;
	}
}
