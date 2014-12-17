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
JLoader::register('SermonspeakerHelperSermonspeaker', JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

/**
 * Plug-in to show the PixelOut player from http://http://wpaudioplayer.com/standalone/
 *
 * @since  5.3.0
 */
class PlgSermonspeakerPixelout extends SermonspeakerPluginPlayer
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
	 * Creates the player
	 *
	 * @param   string                    $context  The context from where it's triggered
	 * @param   object                    &$player  Player object
	 * @param   array|object              $items    An array of sermnon objects or a single sermon object
	 * @param   Joomla\Registry\Registry  $config   A config object. Special properties:
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

		$count = $this->params->get('count', 1);

		// Detect mode and which filetype to use
		if (is_object($items) && !$this->isSupported($items))
		{
			return;
		}

		$this->player->player   = $this->_name;
		$this->player->toggle   = false;
		$this->player->hideInfo = true;
		$this->loadLanguage();

		if (is_array($items))
		{
			$playlist = $this->createMultiPlaylist($items);

			$this->player->mspace = '<div id="mediaspace' . $count . '">'
				. JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH')
				. '</div>';
		}
		else
		{
			$playlist = $this->createSinglePlaylist($items);

			// Special mediaspace to have a HTML5 fallback.
			$this->player->mspace = '<div id="mediaspace' . $count . '">'
				. '<audio src="' . $playlist['soundFile'] . '" controls="controls" preload="auto" >'
				. JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH')
				. '</audio>'
				. '</div>';
		}

		$this->player->script = '<script type="text/javascript">'
			. 'AudioPlayer.embed("mediaspace' . $count . '", {';

		foreach ($playlist as $key => $value)
		{
			if ($value)
			{
				$this->player->script .= $key . ':"' . $value . '",';
			}
		}

		$start = $this->params->get('autostart') ? 'yes' : 'no';
		$this->player->script .= 'autostart:"' . $start . '"'
			. '})'
			. '</script>';

		$this->setPopup('a');

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtml::script('media/plg_sermonspeaker_pixelout/audio-player.js');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('
				AudioPlayer.setup("' . JURI::root() . 'media/plg_sermonspeaker_pixelout/player.swf", {
					width: "' . $this->params->get('awidth', '100%') . '",
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');

			self::$script_loaded = 1;
		}

		return;
	}

	/**
	 * Checks if either audio or videofile is supported
	 *
	 * @param   object  $item  Sermon object
	 *
	 * @return  boolean  supported file
	 */
	private function isSupported($item)
	{
		if (!$item->audiofile)
		{
			return false;
		}

		if (JFile::getExt($item->audiofile) == 'mp3')
		{
			return true;
		}

		return false;
	}

	/**
	 * Generate Playlist for multiple sermons
	 *
	 * @param   array  $items  Array of sermon objects
	 *
	 * @return  array  $playlist  An array containing the files, titles and speakers
	 */
	private function createMultiPlaylist($items)
	{
		$files   = array();
		$titles  = array();
		$artists = array();

		foreach ($items as $item)
		{
			if ($item->audiofile)
			{
				$files[] = urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->audiofile));
			}
			else
			{
				$files[]   = urlencode(JURI::root());
				$titles[]  = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
				$artists[] = '';

				continue;
			}

			$titles[]  = ($item->title) ? urlencode($item->title) : '';
			$artists[] = ($item->speaker_title) ? urlencode($item->speaker_title) : '';
		}

		$playlist = array();
		$playlist['soundFile'] = implode(',', $files);
		$playlist['titles']    = implode(',', $titles);
		$playlist['artists']   = implode(',', $artists);

		return $playlist;
	}

	/**
	 * Generate Playlist for single sermon
	 *
	 * @param   object  $item  A single sermon object
	 *
	 * @return  array  $playlist  An array containing the file, title and speaker
	 */
	private function createSinglePlaylist($item)
	{
		$playlist = array();
		$playlist['soundFile'] = SermonspeakerHelperSermonspeaker::makeLink($item->audiofile);
		$playlist['titles']    = ($item->title) ? urlencode($item->title) : '';
		$playlist['artists']   = ($item->speaker_title) ? urlencode($item->speaker_title) : '';

		return $playlist;
	}
}
