<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';

/**
 * Flowplayer 5
 *
 * @since  5
 */
class SermonspeakerHelperPlayerFlowplayer5 extends SermonspeakerHelperPlayer
{
	/**
	 * @var bool
	 *
	 * @since ?
	 */
	private static $script_loaded;

	/**
	 * @var string
	 *
	 * @since ?
	 */
	public $mode;

	/**
	 * Checks the filename if it's supported by the player
	 *
	 * @param   string $file Filename
	 *
	 * @return  mixed  Mode (audio or video) or false when not supported
	 *
	 * @since ?
	 */
	public function isSupported($file)
	{
		$ext = JFile::getExt($file);
		$video_ext = array('mp4', 'webm', 'ogg', 'ogv', 'avi', 'm3u8', 'ts');

		if (in_array($ext, $video_ext))
		{
			$this->mode = 'video';
		}
		else
		{
			$this->mode = false;
		}

		return $this->mode;
	}

	/**
	 * Gets name of player
	 *
	 * @return  string  Name of player
	 *
	 * @since ?
	 */
	public function getName()
	{
		return 'Flowplayer 5';
	}

	/**
	 * Prepares the player
	 *
	 * @param   object $item   Itemobject
	 * @param   array  $config Config array
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function preparePlayer($item, $config)
	{
		$this->config = $config;
		$this->player = 'FlowPlayer5';
		$this->toggle = $this->params->get('fileswitch', 0);

		if (is_array($item))
		{
			$this->setDimensions('23px', '100%');

			// Make sure to not use < or && in JavaScript code as it will break XHTML compatibility
			$options['onStart'] = 'function(){'
				. 'for (var i = 0; jQuery("#sermon"+i).length; i++){'
				. 'jQuery("#sermon"+i).removeClass("ss-current");'
				. '}'
				. 'var entry = flowplayer().getClip();'
				. 'jQuery("#sermon"+entry.index).addClass("ss-current");'
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
				. 'jQuery("#playing-pic").attr("src", entry.coverImage);'
				. 'if(entry.coverImage){'
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
				. '}';
			$this->toggle = $this->params->get('fileswitch', 0);
			$type = ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->config['prio'])) ? 'a' : 'v';
			$entries = array();
			$file = '';

			foreach ($item as $temp_item)
			{
				$entry = array();

				// Choose picture to show
				$img = SermonspeakerHelperSermonspeaker::insertPicture($temp_item, 1);

				// Choosing the default file to play based on prio and availabilty
				if ($this->config['type'] == 'auto')
				{
					$file = SermonspeakerHelperSermonspeaker::getFileByPrio($temp_item, $this->config['prio']);
				}
				else
				{
					$file = ($this->config['type'] == 'video') ? $temp_item->videofile : $temp_item->audiofile;
				}

				if ($file)
				{
					$entry['url'] = SermonspeakerHelperSermonspeaker::makeLink($file);
				}
				else
				{
					$entry['url'] = ($img) ? $img : JUri::base(true) . '/media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
				}

				$entries[] = $entry['url'];
			}

			$this->playlist['default'] = implode(',', $entries);
		}
		else
		{
			$this->setDimensions('23px', '300px');
			$type = ($this->mode == 'audio') ? 'a' : 'v';
			$file = ($type == 'a') ? $item->audiofile : $item->videofile;
			$file = SermonspeakerHelperSermonspeaker::makeLink($file);
			$this->playlist['default'] = $file;
		}

		$this->setPopup($type);
		$this->mspace = '<div id="mediaspace' . $this->config['count'] . '"></div>';
		$this->script = '<script type="text/javascript">'
			. 'jQuery("#mediaspace' . $this->config['count'] . '").flowplayer({'
			. 'playlist: ["' . $file . '"]'
			. '});'
			. '</script>';

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtmlJQuery::framework();
			JHtml::Script('media/com_sermonspeaker/player/flowplayer5/flowplayer.min.js');
			JHtml::_('stylesheet', 'media/com_sermonspeaker/player/flowplayer5/skin/minimalist.css');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("function ss_play(id){flowplayer('mediaspace" . $this->config['count'] . "').play(parseInt(id));}");

			if ($this->toggle)
			{
				$awidth = is_numeric($this->config['awidth']) ? $this->config['awidth'] . 'px' : $this->config['awidth'];
				$aheight = is_numeric($this->config['aheight']) ? $this->config['aheight'] . 'px' : $this->config['aheight'];
				$vwidth = is_numeric($this->config['vwidth']) ? $this->config['vwidth'] . 'px' : $this->config['vwidth'];
				$vheight = is_numeric($this->config['vheight']) ? $this->config['vheight'] . 'px' : $this->config['vheight'];

				if (!is_array($item))
				{
					$url = 'index.php?&task=download&id=' . $item->slug . '&type=';
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
						flowplayer().play([' . $this->playlist['video'] . ']);
						document.getElementById("mediaspace' . $this->config['count'] . '").style.width="' . $vwidth . '";
						document.getElementById("mediaspace' . $this->config['count'] . '").style.height="' . $vheight . '";
						' . $download_video . '
					}
				');
				$doc->addScriptDeclaration('
					function Audio() {
						flowplayer().play([' . $this->playlist['audio'] . ']);
						document.getElementById("mediaspace' . $this->config['count'] . '").style.width="' . $awidth . '";
						document.getElementById("mediaspace' . $this->config['count'] . '").style.height="' . $aheight . '";
						' . $download_audio . '
					}
				');
			}

			self::$script_loaded = 1;
		}

		return;
	}
}
