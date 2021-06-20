<?php
/**
 * @package         SermonSpeaker
 * @subpackage      Plugin.SermonSpeaker
 * @author          Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\HTML\HTMLHelper;

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
	public function onGetPlayer($context, $player, $items, $config)
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

		$fileprio = $this->params->get('fileprio');
		$type     = $this->params->get('type', 'auto');
		$count    = $this->params->get('count', 1);
		$toggle   = $this->params->get('filetoggle');

		$this->loadLanguage();

		// Detect mode and which filetype to use
		if (is_array($items))
		{
			$mode1 = ($type == 'audio' || ($type == 'auto' && !$fileprio)) ? 'audio' : 'video';
		}
		else
		{
			$supported = $this->isSupported($items);

			if (!$supported)
			{
				return;
			}

			if ($type != 'auto')
			{
				if (!in_array($type, $supported))
				{
					return;
				}

				$mode1 = $type;
			}
			else
			{
				if (count($supported) == 1)
				{
					// Only one file is supported
					$mode1  = $supported[0];
					$toggle = false;
				}
				else
				{
					// Both files are supported
					$mode1 = $supported[$fileprio];
				}
			}
		}

		$player->player = $this->_name;
		$player->id     = 'mediaspace' . $count;

		// Set width and height for later use
		$dimensions['audiowidth']  = $this->params->get('awidth', '100');
		$dimensions['audioheight'] = $this->params->get('aheight', '40');
		$dimensions['videowidth']  = $this->params->get('vwidth', '600');
		$dimensions['videoheight'] = $this->params->get('vheight', '300');

		$autoplay   = $this->params->get('autostart') ? ' autoplay="autoplay"' : '';
		$field      = $mode1 . 'file';
		$langCode   = explode('-', JFactory::getLanguage()->getTag())[0];
		$stretching = $this->params->get('responsive') ? 'responsive' : 'none';

		$player->mspace = '<' . $mode1 . ' id="' . $player->id . '" class="mejs__player"'
			. $autoplay . ' preload="metadata" controls="controls"'
			. ' width="' . $dimensions[$mode1 . 'width'] . '" height="' . $dimensions[$mode1 . 'height'] . '"'
			. ' data-mejsoptions=\'{"showPlaylist": false, "stretching": "' . $stretching . '",'
			. ' "currentMessage": "' . JText::_('PLG_SERMONSPEAKER_MEDIAELEMENT_CURRENT_MESSAGE') . '",'
			. ' "features": ["playpause", "prevtrack", "nexttrack", "current", "progress", "duration", "volume", "playlist", "fullscreen", "speed"]}\''
			. '>';

		if (is_array($items))
		{
			foreach ($items as $item)
			{
				$file = $item->$field;
				$player->mspace .= $this->createSource($item, $file);
			}
		}
		else
		{
			$player->mspace .= $this->createSource($items, $items->$field);
		}

		$player->mspace .= '</' . $mode1 . '>';

		$player->toggle = $toggle;

		if ($toggle)
		{
			$mode  = ($mode1 == 'audio') ? 'video' : 'audio';
			$field = $mode . 'file';
			$player->mspace .= '<' . $mode . ' id="' . $player->id . '-other" class="mejs__player hidden"'
				. $autoplay . ' preload="metadata" controls="controls"'
				. ' width="' . $dimensions[$mode . 'width'] . '" height="' . $dimensions[$mode . 'height'] . '"'
				. ' data-mejsoptions=\'{"showPlaylist": false, "stretching": "' . $stretching . '",'
				. ' "currentMessage": "' . JText::_('PLG_SERMONSPEAKER_MEDIAELEMENT_CURRENT_MESSAGE') . '",'
				. ' "features": ["playpause", "prevtrack", "nexttrack", "current", "progress", "duration", "volume", "playlist", "fullscreen", "speed"]}\''
				. '>';

			if (is_array($items))
			{
				foreach ($items as $item)
				{
					$file = $item->$field;
					$player->mspace .= $this->createSource($item, $file);
				}
			}
			else
			{
				$player->mspace .= $this->createSource($items, $items->$field);
			}

			$player->mspace .= '</' . $mode . '>';
		}

		$this->loadLanguage();

		// Popup Dimensions
		$this->player->popup['width']  = $dimensions[$mode1 . 'width'] + 130;
		$this->player->popup['height'] = $dimensions[$mode1 . 'height'] + (int) $this->c_params->get('popup_height', 150);

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			HtmlHelper::_('jquery.framework');

			JFactory::getDocument()->addScriptDeclaration('mejs.i18n.language(\'' . $langCode . '\');');
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/mediaelement-and-player.min.js', array('relative' => true));
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/mediaelement-and-player.min.js',  array('relative' => true));
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/renderers/vimeo.min.js',  array('relative' => true));
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/renderers/facebook.min.js',  array('relative' => true));
			JHtml::_('script', 'plg_sermonspeaker_mediaelement/lang/' . $langCode . '.js',  array('relative' => true));
			JHtml::_('stylesheet', 'plg_sermonspeaker_mediaelement/mediaelementplayer.min.css',  array('relative' => true));

			if ($this->params->get('speedplugin'))
			{
				JHtml::_('script', 'plg_sermonspeaker_mediaelement/speed/speed.min.js',  array('relative' => true));
				JHtml::_('script', 'plg_sermonspeaker_mediaelement/speed/speed-i18n.js',  array('relative' => true));
				JHtml::_('stylesheet', 'plg_sermonspeaker_mediaelement/speed/speed.min.css',  array('relative' => true));
			}

			if (is_array($items))
			{
				JHtml::_('script', 'plg_sermonspeaker_mediaelement/playlist/playlist.min.js',  array('relative' => true));
				JHtml::_('script', 'plg_sermonspeaker_mediaelement/playlist/playlist-i18n.js',  array('relative' => true));
				JHtml::_('stylesheet', 'plg_sermonspeaker_mediaelement/playlist/playlist.min.css',  array('relative' => true));
				JHtml::_('script', 'plg_sermonspeaker_mediaelement/sermonspeaker.js',  array('relative' => true));
			}

			self::$script_loaded = 1;
		}
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

		if (parse_url($item->audiofile, PHP_URL_HOST) == 'drive.google.com')
		{
			$supported[] = 'audio';
		}

		if (in_array(JFile::getExt(strtok($item->videofile, '?')), $video_ext))
		{
			$supported[] = 'video';
		}

		if (parse_url($item->videofile, PHP_URL_HOST) == 'drive.google.com')
		{
			$supported[] = 'video';
		}

		$host = parse_url($item->videofile, PHP_URL_HOST);

		if ($host == 'youtube.com' || $host == 'www.youtube.com' || $host == 'youtu.be')
		{
			$supported[] = 'video';
		}

		if (parse_url($item->videofile, PHP_URL_HOST) == 'vimeo.com'
			|| parse_url($item->videofile, PHP_URL_HOST) == 'player.vimeo.com'
		)
		{
			$supported[] = 'video';
		}

		if (parse_url($item->videofile, PHP_URL_HOST) == 'www.facebook.com'
			|| parse_url($item->videofile, PHP_URL_HOST) == 'facebook.com'
		)
		{
			$supported[] = 'video';
		}

		return $supported;
	}

	/**
	 * @param $item  object The sermon item
	 * @param $file  string The file to use
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function createSource($item, $file)
	{
		if (!$file)
		{
			$file = '/media/com_sermonspeaker/media/blank.mp3';
			$attributes['error'] = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
		}

		$attributes['type'] = SermonspeakerHelperSermonspeaker::getMime(JFile::getExt($file), false);

		if (!$attributes['type'])
		{
			if (parse_url($file, PHP_URL_HOST) == 'vimeo.com'
				|| parse_url($file, PHP_URL_HOST) == 'player.vimeo.com'
			)
			{
				$attributes['type'] = 'video/vimeo';
			}

			if (!$attributes['type'] && (parse_url($file, PHP_URL_HOST) == 'youtube.com'
					|| parse_url($file, PHP_URL_HOST) == 'www.youtube.com'
					|| parse_url($file, PHP_URL_HOST) == 'youtu.be')
			)
			{
				$attributes['type'] = 'video/youtube';
			}
		}

		if ($img = SermonspeakerHelperSermonspeaker::insertPicture($item, 1))
		{
			$attributes['data-thumbnail'] = $img;
		}

		$attributes['src'] = SermonspeakerHelperSermonspeaker::makeLink($file);
		$attributes['title'] = $item->title;
		$attributes['duration'] = $item->sermon_time;


		$desc           = array();

		if ($item->sermon_date)
		{
			$desc[] = JText::_('JDATE') . ': ' . JHtml::date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true);
		}

		if ($item->speaker_title)
		{
			$desc[] = JText::_('PLG_SERMONSPEAKER_COMMON_SPEAKER') . ': ' . $item->speaker_title;
		}

		$attributes['description'] = implode('<br>', $desc);

		$attrs = '';

		foreach ($attributes as $key => $value)
		{
			$attrs .= ' ' . $key . '="' . $value . '"';
		}

		return '<source' . $attrs . '>';
}
}
