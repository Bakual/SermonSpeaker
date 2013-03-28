<?php
defined('_JEXEC') or die;

require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player.php');

/**
 * Silverlight WMVPlayer
 */
class SermonspeakerHelperPlayerWmvplayer extends SermonspeakerHelperPlayer
{
	private static $script_loaded;

	public function isSupported($file){
		$ext	= JFile::getExt($file);
		if ($ext == 'wma')
		{
			$this->mode	= 'audio';
		}
		elseif ($ext == 'wmv')
		{
			$this->mode	= 'video';
		}
		else
		{
			$this->mode	= false;
		}
		return $this->mode;
	}

	public function getName()
	{
		return 'JW WMV Player';
	}

	public function preparePlayer($item, $config)
	{
		$this->config	= $config;
		$this->player	= $this->getName();

		if (is_array($item))
		{
			$this->mspace	= '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '.$this->player.' doesn\'t support Playlists</div>';
			$this->script	= '';
			return false;
		}

		$player	= JURI::root().'media/com_sermonspeaker/player/wmvplayer/wmvplayer.xaml';
		$start	= $this->config['autostart'] ? 1 : 0;
		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_JAVASCRIPT').'</div>';
		$file	= SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->config['prio']);
		$file	= SermonspeakerHelperSermonspeaker::makeLink($file);
		$type	= (JFile::getExt($file) == 'wma') ? 'a' : 'v';
		$this->setDimensions('21px', '250px');
		$this->setPopup($type);

		$image = SermonspeakerHelperSermonspeaker::insertPicture($item);
		if ($image)
		{
			$image = "'image':'".$image."',";
		}
		$duration = '';
		if ($item->sermon_time != '00:00:00')
		{
			$time_arr = explode(':', $item->sermon_time);
			$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
			$duration = 'duration: '.$seconds.',';
		}
		$start = $this->config['autostart'] ? 'true' : 'false';
		$this->script	= '<script type="text/javascript">'
							.'var elm = document.getElementById("mediaspace'.$this->config['count'].'");'
							.'var cfg = {'
							."	file:'".$file."',"
							.'	autostart:'.$start.','
							.$duration
							.$image
							."	width: '".$this->config[$type.'width']."',"
							."	height: '".$this->config[$type.'height']."'"
							.'};'
							."var ply = new jeroenwijering.Player(elm,'".$player."',cfg);"
						.'</script>';
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