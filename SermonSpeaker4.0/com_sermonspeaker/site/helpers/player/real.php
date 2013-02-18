<?php
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_SITE.'/helpers/player.php');
/**
 * JW Player 5
 */
class SermonspeakerHelperPlayerReal extends SermonspeakerHelperPlayer
{
	private static $script_loaded;

	public function isSupported($file){
		$ext		= JFile::getExt($file);
		$audio_ext	= array('ra', 'ram', 'rm', 'rpm');
		$video_ext	= array('rv');
		if (in_array($ext, $audio_ext))
		{
			// Audio File
			$this->mode	= 'audio';
		}
		elseif (in_array($ext, $video_ext))
		{
			$this->mode	= 'video';
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
		$this->player	= 'RealPlayer';
		$this->toggle	= 0;
		$type			= ($this->mode == 'audio') ? 'a' : 'v';

		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_JAVASCRIPT').'</div>';

		// Setting some general player options
		$autostart	= $this->config['autostart'] ? 'true' : 'false';
		$this->setDimensions('30px', '250px');

		// Detect file to use
		if ($this->config['type'] == 'auto')
		{
			$file	= SermonspeakerHelperSermonspeaker::getFileByPrio($item, $this->config['prio']);
		}
		else
		{
			$file	= ($this->config['type'] == 'video') ? $item->videofile : $item->audiofile;
		}
		$file		= SermonspeakerHelperSermonspeaker::makeLink($file);

		// For controls, see http://service.real.com/help/library/guides/realone/ProductionGuide/HTML/samples/embed/plugin2.htm
		// "ImageWindow" brings the video window, "All" brings all the controls (but not the video), "ControlPanel" only the controls
		$this->mspace	= '<object id="mediaspace'.$this->config['count'].'" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" height="'.$this->config[$type.'height'].'" width="'.$this->config[$type.'width'].'">'
						.'<param name="controls" value="ControlPanel">'
						.'<param name="console" value="media'.$this->config['count'].'">'
						.'<param name="autostart" value="'.$autostart.'">'
						.'<param name="src" value="'.$file.'">'
						.'<embed height="'.$this->config[$type.'height'].'" width="'.$this->config[$type.'width'].'" controls="ImageWindow" src="'.$file.'" type="audio/x-pn-realaudio-plugin" autostart='.$autostart.'>'
					.'</object>';
		// Add video before controls
		if ($this->config['type'] == 'video')
		{
			$this->mspace	= '<object id="mediaspace'.$this->config['count'].'" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" height="'.$this->config[$type.'height'].'" width="'.$this->config[$type.'width'].'">'
						.'<param name="controls" value="ImageWindow">'
						.'<param name="console" value="media'.$this->config['count'].'">'
						.'<embed height="'.$this->config[$type.'height'].'" width="'.$this->config[$type.'width'].'" controls="ImageWindow" src="'.$file.'" type="audio/x-pn-realaudio-plugin">'
					.'</object><br>'
					.$this->mspace;
		}
		$this->script	= '';
		return;
	}
}