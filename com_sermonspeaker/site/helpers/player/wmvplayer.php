<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';

/**
 * Silverlight WMVPlayer
 *
 * @since  5
 */
class SermonspeakerHelperPlayerWmvplayer extends SermonspeakerHelperPlayer
{
	private static $script_loaded;

	/**
	 * Checks the filename if it's supported by the player
	 *
	 * @param   string  $file  Filename
	 *
	 * @return  mixed  Mode (audio or video) or false when not supported
	 */
	public function isSupported($file)
	{
		$ext	= JFile::getExt($file);

		if ($ext == 'wma')
		{
			$this->mode = 'audio';
		}
		elseif ($ext == 'wmv')
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
	 */
	public function getName()
	{
		return 'JW WMV Player';
	}

	/**
	 * Prepares the player
	 *
	 * @param   object  $item    Itemobject
	 * @param   array   $config  Config array
	 *
	 * @return  void
	 */
	public function preparePlayer($item, $config)
	{
		$this->config = $config;
		$this->player = $this->getName();

		if (is_array($item))
		{
			$this->mspace = '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '
							. $this->player . ' doesn\'t support Playlists</div>';
			$this->script = '';

			return;
		}

		$player = JUri::root() . 'media/com_sermonspeaker/player/wmvplayer/wmvplayer.xaml';
		$this->mspace = '<div id="mediaspace' . $this->config['count'] . '">' . JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_JAVASCRIPT') . '</div>';
		$file = SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->config['prio']);
		$file = SermonspeakerHelperSermonspeaker::makeLink($file);
		$type = (JFile::getExt($file) == 'wma') ? 'a' : 'v';
		$this->setDimensions('21px', '250px');
		$this->setPopup($type);

		$image = SermonspeakerHelperSermonspeaker::insertPicture($item);

		if ($image)
		{
			$image = "'image':'" . $image . "',";
		}

		$duration = '';

		if ($item->sermon_time != '00:00:00')
		{
			$time_arr = explode(':', $item->sermon_time);
			$seconds  = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
			$duration = 'duration: ' . $seconds . ',';
		}

		$start = $this->config['autostart'] ? 'true' : 'false';
		$this->script = '<script type="text/javascript">'
						. 'var elm = document.getElementById("mediaspace' . $this->config['count'] . '");'
						. 'var cfg = {'
						. " file:'" . $file . "',"
						. ' autostart:' . $start . ','
						. $duration
						. $image
						. " width: '" . $this->config[$type . 'width'] . "',"
						. " height: '" . $this->config[$type . 'height'] . "'"
						. '};'
						. "var ply = new jeroenwijering.Player(elm,'" . $player . "',cfg);"
					. '</script>';
		$this->toggle = false;

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtml::Script('media/com_sermonspeaker/player/wmvplayer/silverlight.js');
			JHtml::Script('media/com_sermonspeaker/player/wmvplayer/wmvplayer.js');
			self::$script_loaded = 1;
		}

		return;
	}
}
