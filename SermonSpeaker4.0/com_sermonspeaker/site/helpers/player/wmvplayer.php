<?php
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_SITE.'/helpers/player.php');
/**
 * JW Player 5
 */
class SermonspeakerHelperPlayerWmvplayer extends SermonspeakerHelperPlayer {
	public function isSupported($item){
		if ($ext == 'wmv' || $ext == 'wma')
		{
			// WMV Player
			return 'wmvplayer';
		}
		else
		{
			$this->mode	= false;
		}
		return $this->mode;
	}

	public function preparePlayer($item, $config)
	{
		$this->config	= $config;
		$this->player	= 'WMVPlayer';

		$player	= JURI::root().'media/com_sermonspeaker/player/wmvplayer/wmvplayer.xaml';
		$start	= $this->config['autostart'] ? 1 : 0;
		$type	= ($this->status == 'audio') ? 'a' : 'v';
		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_JAVASCRIPT').'</div>';
		$file	= SermonspeakerHelperSermonspeaker::getFileByPrio($temp_item, $this->config['prio']);
		$file	= SermonspeakerHelperSermonspeaker::makeLink($file);
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
		if (!self::$wmvscript)
		{
			JHTML::Script('media/com_sermonspeaker/player/wmvplayer/silverlight.js');
			JHTML::Script('media/com_sermonspeaker/player/wmvplayer/wmvplayer.js');
			self::$wmvscript = 1;
		}
		return;
	}
}