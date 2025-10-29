<?php
/**
 * @package         SermonSpeaker
 * @subpackage      Plugin.SermonSpeaker
 * @author          Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright       © 2020 - Thomas Hunziker
 * @license         http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

JLoader::register('SermonspeakerPluginPlayer', JPATH_SITE . '/components/com_sermonspeaker/plugin/player.php');
JLoader::register('SermonspeakerHelperSermonspeaker', JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

/**
 * Plug-in to show the JW Player 7 from http://www.jwplayer.com/
 *
 * @since  5.4.0
 */
class PlgSermonspeakerJwplayer7 extends SermonspeakerPluginPlayer
{
	/**
	 * @var boolean  True if scripts are loaded already
	 *
	 * @since 5.x
	 */
	private static $script_loaded = false;
	/**
	 * @var object  Holds the player object
	 *
	 * @since 5.x
	 */
	protected $player;
	/**
	 * @var Registry Component Parameters
	 *
	 * @since 5.x
	 */
	protected $c_params;
	/**
	 * @var string player mode. Either 'audio' or 'video'.
	 *
	 * @since 5.x
	 */
	private $mode;
	/**
	 * @var string filetype mode. Either 'audio', 'video' or 'auto' (default).
	 *
	 * @since 5.x
	 */
	private $type;
	/**
	 * @var int which file to prioritise. Either 0 (audio) or 1 (video).
	 *
	 * @since 5.x
	 */
	private $fileprio;
	/**
	 * @var array Player options
	 *
	 * @since 5.x
	 */
	private $options;

	/**
	 * Creates the player
	 *
	 * @param   string                    $context  The context from where it's triggered
	 * @param   object                   &$player   Player object
	 * @param   array|object              $items    An array of sermnon objects or a single sermon object
	 * @param   Registry                  $config   A config object. Special properties:
	 *                                              - count (id of the player)
	 *                                              - type (may be audio, video or auto)
	 *                                              - prio (may be 0 for audio or 1 for video)
	 *                                              - autostart (overwrites the backend setting)
	 *                                              - alt_player (overwrites the backend setting)
	 *                                              - awidth, aheight (width and height for audio)
	 *                                              - vwidth, vheight (width and height for video)
	 *
	 * @since 5.x
	 *
	 * @return  void
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

		$this->player->player = $this->_name;
		$this->player->mspace = '<div id="mediaspace' . $count . '">Loading Player...</div>';
		$this->player->toggle = $toggle;
		$this->loadLanguage();

		// Setting some general player options
		$this->setOptions();

		if (is_array($items))
		{
			$this->createMultiPlaylist($items);
		}
		else
		{
			$this->createSinglePlaylist($items);
		}

		foreach ($this->options as $key => $value)
		{
			$this->options[$key] = $key . ':' . $value;
		}

		$this->setPopup($this->mode[0]);

		// Set width and height for later use
		$dimensions['audiowidth']  = $this->params->get('awidth', '100%');
		$dimensions['audioheight'] = $this->params->get('aheight', '33px');
		$dimensions['videowidth']  = $this->params->get('vwidth', '100%');
		$dimensions['videoheight'] = $this->params->get('vheight', '300px');

		$this->player->script = '<script type="text/javascript">'
			. "jwplayer('mediaspace" . $count . "').setup({"
			. "playlist:[" . $this->player->playlist['default'] . "],"
			. "width:'" . $dimensions[$this->mode . 'width'] . "',"
			. "height:'" . $dimensions[$this->mode . 'height'] . "',"
			. implode(',', $this->options)
			. '})'
			. '</script>';

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			$doc = Factory::getDocument();

			HTMLHelper::_('jquery.framework');

			if ($this->params->get('hosting'))
			{
				HTMLHelper::_('script', $this->params->get('cloud_library_url'));
			}
			else
			{
				HTMLHelper::_('script', 'media/plg_sermonspeaker_jwplayer7/jwplayer.js');
				$doc->addScriptDeclaration('jwplayer.key="' . $this->params->get('license_self') . '";');
			}

			$doc->addScriptDeclaration("function ss_play(id){jwplayer('mediaspace" . $count . "').playlistItem(id);}");

			if ($this->player->toggle)
			{
				if (!is_array($items))
				{
					$url            = 'index.php?&task=download&id=' . $items->slug . '&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\'' . Route::_($url . 'video')
						. '\'};document.getElementById("sermon_download").value="' . Text::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO') . '"';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\'' . Route::_($url . 'audio')
						. '\'};document.getElementById("sermon_download").value="' . Text::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO') . '"';
				}
				else
				{
					$download_video = '';
					$download_audio = '';
				}

				$doc->addScriptDeclaration('
					function Video() {
						jwplayer().load([' . $this->player->playlist['video'] . ']).resize("' . $dimensions['videowidth'] . '","' . $dimensions['videoheight'] . '");
						document.getElementById("mediaspace' . $count . '_wrapper").style.width="' . $dimensions['videowidth'] . '";
						document.getElementById("mediaspace' . $count . '_wrapper").style.height="' . $dimensions['videoheight'] . '";
						' . $download_video . '
					}
				');
				$doc->addScriptDeclaration('
					function Audio() {
						jwplayer().load([' . $this->player->playlist['audio'] . ']).resize("' . $dimensions['audiowidth'] . '","' . $dimensions['audioheight'] . '");
						document.getElementById("mediaspace' . $count . '_wrapper").style.width="' . $dimensions['audiowidth'] . '";
						document.getElementById("mediaspace' . $count . '_wrapper").style.height="' . $dimensions['audioheight'] . '";
						' . $download_audio . '
					}
				');
			}

			self::$script_loaded = 1;
		}
	}

	/**
	 * Checks if either audio or videofile is supported
	 *
	 * @param   object  $item  Sermon object
	 *
	 * @return  array  supported files
	 * @since 5.x
	 *
	 */
	private function isSupported(object $item): array
	{
		$supported = array();

		if (!$item->audiofile && !$item->videofile)
		{
			return $supported;
		}

		// Define supported file extensions
		$audio_ext = array('aac', 'm4a', 'f4a', 'mp3', 'ogg', 'oga');
		$video_ext = array('mp4', 'm4v', 'f4v', 'mov', 'flv', 'webm');

		if (in_array(File::getExt(strtok($item->audiofile, '?')), $audio_ext))
		{
			$supported[] = 'audio';
		}

		if (in_array(File::getExt(strtok($item->videofile, '?')), $video_ext))
		{
			$supported[] = 'video';
		}

		$host = parse_url($item->videofile, PHP_URL_HOST);

		if ($host == 'youtube.com' || $host == 'www.youtube.com' || $host == 'youtu.be')
		{
			$supported[] = 'video';
		}

		return $supported;
	}

	/**
	 * Set generic options
	 *
	 * @since 5.x
	 */
	private function setOptions()
	{
		if (!$this->params->get('mode'))
		{
			$this->options['primary'] = '"flash"';
		}

		$this->options['autostart'] = $this->params->get('autostart') ? 'true' : 'false';

		// Skin
		$skinOptions = array();
		$skinName    = $this->params->get('skin', 'seven');

		// Load CSS file from media folder
		$file = 'plg_sermonspeaker_jwplayer7/' . $skinName . '.css';
		HTMLHelper::_('stylesheet', $file, array('relative' => true));

		$skinOptions[] = "name:'" . $skinName . "'";

		if ($skinActive = $this->params->get('skin_active'))
		{
			$skinOptions[] = "active:'" . $skinActive . "'";
		}

		if ($skinInactive = $this->params->get('skin_inactive'))
		{
			$skinOptions[] = "inactive:'" . $skinInactive . "'";
		}

		if ($skinBackground = $this->params->get('skin_background'))
		{
			$skinOptions[] = "background:'" . $skinBackground . "'";
		}

		$this->options['skin'] = '{' . implode(',', $skinOptions) . '}';

		// Responsive
		if ($this->params->get('responsive') && $this->mode == 'video')
		{
			$this->options['aspectratio'] = "'" . $this->params->get('aspectratio', '4:3') . "'";
		}

		// Plugins
		if ($this->params->get('ga'))
		{
			$this->options['ga'] = '{}';
		}

		if ($this->params->get('share'))
		{
			$this->options['sharing'] = '{}';
		}

		// Don't show title, description and visual playlist within the player.
		$this->options['displaytitle']       = 'false';
		$this->options['displaydescription'] = 'false';
		$this->options['visualplaylist']     = 'false';
	}

	/**
	 * Generate Playlist for multiple sermons
	 *
	 * @param   array  $items  Array of sermon objects
	 *
	 * @return  void
	 * @since 5.x
	 *
	 */
	private function createMultiPlaylist(array $items)
	{
		$this->setDimensions('33', '100%');

		// Make sure to not use < or && in JavaScript code as it will break XHTML compatibility
		$this->options['events'] = '{'
			. 'onPlaylistItem: function(event){'
			. 'for (var i = 0; jQuery("#sermon"+i).length; i++){'
			. 'jQuery("#sermon"+i).removeClass("ss-current");'
			. '}'
			. 'jQuery("#sermon"+event.index).addClass("ss-current");'
			. 'var entry = jwplayer().getPlaylistItem();'
			. 'if (entry.duration > 0){'
			. 'time = new Array();'
			. 'var hrs = Math.floor(entry.duration/3600);'
			. 'if (hrs > 0){time.push(hrs);}'
			. 'var min = Math.floor((entry.duration - hrs * 3600)/60);'
			. 'if (hrs == 0 || min >= 10){'
			. 'time.push(min);'
			. '} else {'
			. 'time.push("0" + min);'
			. '}'
			. 'var sec = entry.duration - hrs * 3600 - min * 60;'
			. 'if (sec >= 10){'
			. 'time.push(sec);'
			. '} else {'
			. 'time.push("0" + sec);'
			. '}'
			. 'var duration = time.join(":");'
			. 'jQuery("#playing-duration").html(duration);'
			. '} else {'
			. 'jQuery("#playing-duration").html("");'
			. '}'
			. 'jQuery("#playing-pic").attr("src", entry.image);'
			. 'if(entry.image){'
			. 'jQuery("#playing-pic").show();'
			. '}else{'
			. 'jQuery("#playing-pic").hide();'
			. '}'
			. 'if(entry.error){'
			. 'jQuery("#playing-error").html(entry.error);'
			. 'jQuery("#playing-error").show();'
			. '}else{'
			. 'jQuery("#playing-error").hide();'
			. '}'
			. 'jQuery("#playing-title").html(entry.title);'
			. 'jQuery("#playing-desc").html(entry.description);'
			. '}'
			. '}';
		$entries                 = array();
		$audios                  = array();
		$videos                  = array();

		foreach ($items as $item)
		{
			$entry = array();

			if ($this->type == 'auto')
			{
				// Choosing the default file to play based on prio and availabilty
				$file = SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->fileprio);
			}
			else
			{
				$property = $this->type . 'file';
				$file     = $item->$property;
			}

			if ($file)
			{
				$entry['file'] = addslashes(SermonspeakerHelperSermonspeaker::makeLink($file));
			}
			else
			{
				$entry['file']  = Uri::base(true) . '/media/com_sermonspeaker/media/blank.mp3';
				$entry['error'] = Text::_('JGLOBAL_RESOURCE_NOT_FOUND');
			}

			$entry['title'] = addslashes($item->title);
			$desc           = array();

			if ($item->sermon_date)
			{
				// Todo: Pick correct date format (from component or add param to plugin?)
				$desc[] = Text::_('JDATE') . ': ' . HTMLHelper::date($item->sermon_date, Text::_($this->c_params->get('date_format')));
			}

			if ($item->speaker_title)
			{
				$desc[] = Text::_('PLG_SERMONSPEAKER_COMMON_SPEAKER') . ': ' . addslashes($item->speaker_title);
			}

			$entry['description'] = implode('\x3Cbr />', $desc);

			if ($item->sermon_time != '00:00:00')
			{
				$time_arr          = explode(':', $item->sermon_time);
				$seconds           = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
				$entry['duration'] = $seconds;
			}

			if ($img = SermonspeakerHelperSermonspeaker::insertPicture($item, 1))
			{
				$entry['image'] = $img;
			}

			foreach ($entry as $key => $value)
			{
				$entry[$key] = $key . ":'" . $value . "'";
			}

			$entries[] = '{' . implode(',', $entry) . '}';

			if ($this->player->toggle)
			{
				// Preparing specific playlists for audio and video
				if ($item->audiofile)
				{
					$file = addslashes(SermonspeakerHelperSermonspeaker::makeLink($item->audiofile));
					unset($entry['error']);
				}
				else
				{
					$file           = Uri::base(true) . '/media/com_sermonspeaker/media/blank.mp3';
					$entry['error'] = "error:'" . Text::_('JGLOBAL_RESOURCE_NOT_FOUND') . "'";
				}

				$entry['file'] = "file:'" . $file . "'";
				$audios[]      = '{' . implode(',', $entry) . '}';

				if ($item->videofile)
				{
					$file = addslashes(SermonspeakerHelperSermonspeaker::makeLink($item->videofile));
					unset($entry['error']);
				}
				else
				{
					$file           = Uri::base(true) . '/media/com_sermonspeaker/media/blank.mp3';
					$entry['error'] = "error:'" . Text::_('JGLOBAL_RESOURCE_NOT_FOUND') . "'";
				}

				$entry['file'] = "file:'" . $file . "'";
				$videos[]      = '{' . implode(',', $entry) . '}';
			}
		}

		$this->player->playlist['default'] = implode(',', $entries);

		if ($this->player->toggle)
		{
			$this->player->playlist['audio'] = implode(',', $audios);
			$this->player->playlist['video'] = implode(',', $videos);
		}
	}

	/**
	 * Generate Playlist for single sermon
	 *
	 * @param   object  $item  A single sermon object
	 *
	 * @return  void
	 * @since 5.x
	 *
	 */
	private function createSinglePlaylist(object $item)
	{
		$this->setDimensions('33', '100%');

		$entry = array();

		$property = $this->mode . 'file';
		$file     = $item->$property;

		$entry['file'] = SermonspeakerHelperSermonspeaker::makeLink($file);

		if ($img = SermonspeakerHelperSermonspeaker::insertPicture($item, 1))
		{
			$entry['image'] = $img;
		}

		if ($item->sermon_time != '00:00:00')
		{
			$time_arr          = explode(':', $item->sermon_time);
			$seconds           = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
			$entry['duration'] = $seconds;
		}

		foreach ($entry as $key => $value)
		{
			$entry[$key] = $key . ":'" . $value . "'";
		}

		$this->player->playlist['default'] = '{' . implode(',', $entry) . '}';

		if ($this->player->toggle)
		{
			if ($item->audiofile && $item->videofile)
			{
				$this->player->playlist['audio'] = "{file:'" . SermonspeakerHelperSermonspeaker::makeLink($item->audiofile) . "'}";
				$this->player->playlist['video'] = "{file:'" . SermonspeakerHelperSermonspeaker::makeLink($item->videofile) . "'}";
			}
			else
			{
				$this->player->toggle = false;
			}
		}
	}
}
