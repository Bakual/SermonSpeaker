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
 * Plug-in to call a 3rd party plugin to show the player
 *
 * @since  5.3.0
 */
class PlgSermonspeakerJwplayer5 extends SermonspeakerPluginPlayer
{
	/**
	 * @var int Increments with each call of the player
	 */
	private static $counter = 0;

	/**
	 * @var boolean  True if scripts are loaded already
	 */
	private static $script_loaded = false;

	/**
	 * @var string player mode. Either 'audio' or 'video'.
	 */
	private $mode;

	/**
	 * @var string player mode. Either 'a' or 'v'.
	 */
	private $type;

	/**
	 * @var array Player options
	 */
	private $options;

	/**
	 * Creates the player
	 *
	 * @param   string        $context  The context from where it's triggered
	 * @param   array|object  $items    An array of sermnon objects or a single sermon object
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
		if (isset($config['alt_player']) && ($config['alt_player'] != 'jwplayer5'))
		{
			return false;
		}

		// Merge $config into plugin params
		{
			$registry = new Joomla\Registry\Registry;
			$registry->loadArray($config);
			$this->params->merge($registry);
		}

		$fileprio = $this->params->get('fileprio');

		// Precheck if player even supports sermon and set mode
		if (is_array($items))
		{
			$this->mode = $fileprio ? 'video' : 'audio';
			$this->type = $fileprio ? 'v' : 'a';
		}
		else
		{
			$audiofile = $this->isSupported($items->audiofile);
			$videofile = $this->isSupported($items->videofile);

			if (!$audiofile && !$videofile)
			{
				return false;
			}

			if ($audiofile && (!$fileprio || !$videofile))
			{
				$this->mode = $audiofile;
			}
			elseif ($videofile && ($fileprio || !$audiofile))
			{
				$this->mode = $videofile;
			}

			$this->type = ($this->mode == 'video') ? 'v' : 'a';
		}

		// Increment counter
		self::$counter++;

		$this->player->player = 'JWPlayer';
		$this->player->mspace = '<div id="mediaspace' . self::$counter . '">Loading Player...</div>';
		$this->player->toggle = $this->params->get('filetoggle');

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

		$this->setPopup($this->type);

		$this->player->script = '<script type="text/javascript">'
			. "jwplayer('mediaspace" . self::$counter . "').setup({"
			. "playlist:[" . $this->player->playlist['default'] . "],"
			. "width:'" . $this->config[$this->type . 'width'] . "',"
			. "height:'" . $this->config[$this->type . 'height'] . "',"
			. implode(',', $this->options)
			. '});'
			. '</script>';

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtmlJQuery::framework();
			JHtml::script('media/plg_sermonspeaker_jwplayer5/jwplayer.js');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('function ss_play(id){jwplayer().playlistItem(id);}');

			if ($this->player->toggle)
			{
				$awidth  = is_numeric($this->config['awidth']) ? $this->config['awidth'] . 'px' : $this->config['awidth'];
				$aheight = is_numeric($this->config['aheight']) ? $this->config['aheight'] . 'px' : $this->config['aheight'];
				$vwidth  = is_numeric($this->config['vwidth']) ? $this->config['vwidth'] . 'px' : $this->config['vwidth'];
				$vheight = is_numeric($this->config['vheight']) ? $this->config['vheight'] . 'px' : $this->config['vheight'];

				if (!is_array($items))
				{
					$url = 'index.php?&task=download&id=' . $items->slug . '&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\'' . JRoute::_($url . 'video')
						. '\'};document.getElementById("sermon_download").value="' . JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO') . '"';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\'' . JRoute::_($url . 'audio')
						. '\'};document.getElementById("sermon_download").value="' . JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO') . '"';
				}
				else
				{
					$download_video = '';
					$download_audio = '';
				}

				$doc->addScriptDeclaration('
					function Video() {
						jwplayer().load([' . $this->player->playlist['video'] . ']).resize("' . $vwidth . '","' . $vheight . '");
						document.getElementById("mediaspace' . self::$counter . '_wrapper").style.width="' . $vwidth . '";
						document.getElementById("mediaspace' . self::$counter . '_wrapper").style.height="' . $vheight . '";
						' . $download_video . '
					}
				');
				$doc->addScriptDeclaration('
					function Audio() {
						jwplayer().load([' . $this->player->playlist['audio'] . ']).resize("' . $awidth . '","' . $aheight . '");
						document.getElementById("mediaspace' . self::$counter . '_wrapper").style.width="' . $awidth . '";
						document.getElementById("mediaspace' . self::$counter . '_wrapper").style.height="' . $aheight . '";
						' . $download_audio . '
					}
				');
			}

			self::$script_loaded = 1;
		}

		$loaded = true;

		return $this->player;
	}

	/**
	 * Checks if either audio or videofile is supported
	 *
	 * @param   object  $file  Filepath to check
	 *
	 * @return  string/false  Mode (audio or video) or false when not supported
	 */
	private function isSupported($file)
	{
		if (!$file)
		{
			return false;
		}

		$ext       = JFile::getExt($file);
		$audio_ext = array('aac', 'm4a', 'mp3');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2');

		if (in_array($ext, $audio_ext))
		{
			// Audio File
			return 'audio';
		}
		elseif (in_array($ext, $video_ext))
		{
			return 'video';
		}
		elseif (parse_url($file, PHP_URL_HOST) == 'youtube.com' || parse_url($file, PHP_URL_HOST) == 'www.youtube.com')
		{
			return 'video';
		}
		else
		{
			return false;
		}
	}

	/**
	 * Set generic options
	 *
	 * @return  array  Array of options
	 */
	private function setOptions()
	{
		$modes   = array();
		$modes[0] = "{type:'flash', src:'" . JURI::base(true) . "/media/plg_sermonspeaker_jwplayer5/player.swf'}";
		$modes[1] = "{type:'html5'}";
		$modes[2] = "{type:'download'}";
		$this->options['modes'] = ($this->params->get('jwmode', 0))
			? '[' . $modes[1] . ',' . $modes[0] . ',' . $modes[2] . ']'
			: '[' . $modes[0] . ',' . $modes[1] . ',' . $modes[2] . ']';
		$this->options['autostart']  = $this->params->get('autostart') ? 'true' : 'false';
		$this->options['controlbar'] = "'bottom'";

		if ($skin = $this->params->get('skin'))
		{
			$this->options['skin'] = "'" . SermonspeakerHelperSermonspeaker::makeLink($skin) . "'";
		}

		// Plugins
		if ($this->params->get('ga_id', ''))
		{
			$plugins['gapro-2'] = "{idstring:'SermonSpeaker/||provider||:||file||'}";
		}

		if ($this->params->get('fbit', 0))
		{
			$plugins['fbit-1'] = '{}';
		}

		if ($this->params->get('tweetit', 0))
		{
			$plugins['tweetit-1'] = '{}';
		}

		if ($this->params->get('plusone', 0))
		{
			$plugins['plusone-1'] = '{}';
		}

		if ($this->params->get('share', 0))
		{
			$plugins['sharing-3'] = '{}';
		}

		if ($this->params->get('viral', 0))
		{
			$plugins['viral-2'] = '{}';
		}

		if (isset($plugins))
		{
			foreach ($plugins as $key => $value)
			{
				$plugins[$key] = "'" . $key . "':" . $value;
			}

			$this->options['plugins'] = '{' . implode(',', $plugins) . '}';
		}

		return;
	}

	/**
	 * Generate Playlist for multiple sermons
	 *
	 * @param   array  $items  Array of sermon objects
	 *
	 * @return  void
	 */
	private function createMultiPlaylist($items)
	{
		$this->setDimensions('23px', '100%');

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
		$entries = array();
		$audios  = array();
		$videos  = array();

		foreach ($items as $item)
		{
			$entry = array();

			// Choose picture to show
			$img = SermonspeakerHelperSermonspeaker::insertPicture($item, 1);

			// Choosing the default file to play based on prio and availabilty
			$file = SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->params->get('fileprio'));

			if ($file)
			{
				$entry['file'] = SermonspeakerHelperSermonspeaker::makeLink($file);
			}
			else
			{
				$entry['file']  = ($img) ? $img : JURI::base(true) . '/media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
				$entry['error'] = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
			}

			$entry['title'] = addslashes($item->title);
			$desc = array();

			if ($item->sermon_date)
			{
				$desc[] = JText::_('JDATE') . ': ' . JHtml::Date($item->sermon_date, JText::_($this->params->get('date_format')), true);
			}

			if ($item->speaker_title)
			{
				$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER') . ': ' . addslashes($item->speaker_title);
			}

			$entry['description'] = implode('\x3Cbr />', $desc);

			if ($item->sermon_time != '00:00:00')
			{
				$time_arr = explode(':', $item->sermon_time);
				$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
				$entry['duration'] = $seconds;
			}

			if ($img)
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
					$file = SermonspeakerHelperSermonspeaker::makeLink($item->audiofile);
					unset($entry['error']);
				}
				else
				{
					$file = ($img) ? $img : JURI::base(true) . '/media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
					$entry['error'] = "error:'" . JText::_('JGLOBAL_RESOURCE_NOT_FOUND') . "'";
				}

				$entry['file'] = "file:'" . $file . "'";
				$audios[] = '{' . implode(',', $entry) . '}';

				if ($item->videofile)
				{
					$file = SermonspeakerHelperSermonspeaker::makeLink($item->videofile);
					unset($entry['error']);
				}
				else
				{
					$file = ($img) ? $img : JURI::base(true) . '/media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
					$entry['error'] = "error:'" . JText::_('JGLOBAL_RESOURCE_NOT_FOUND') . "'";
				}

				$entry['file'] = "file:'" . $file . "'";
				$videos[] = '{' . implode(',', $entry) . '}';
			}
		}

		$this->player->playlist['default'] = implode(',', $entries);

		if ($this->player->toggle)
		{
			$this->player->playlist['audio'] = implode(',', $audios);
			$this->player->playlist['video'] = implode(',', $videos);
		}

		return;
	}

	/**
	 * Generate Playlist for single sermon
	 *
	 * @param   object  $item  A single sermon object
	 *
	 * @return  void
	 */
	private function createSinglePlaylist($item)
	{
		$this->setDimensions('23px', '250px');

		$entry = array();

		// Detect file to use
		// Todo: Already detected on start of plugin. Reuse that.
		$file = SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->params->get('fileprio'));

		$entry['file'] = SermonspeakerHelperSermonspeaker::makeLink($file);

		if ($img = SermonspeakerHelperSermonspeaker::insertPicture($item, 1))
		{
			$entry['image'] = $img;
		}

		if ($item->sermon_time != '00:00:00')
		{
			$time_arr = explode(':', $item->sermon_time);
			$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
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

		return;
	}
}