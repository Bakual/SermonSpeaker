<?php
defined('_JEXEC') or die;
class SermonspeakerViewFiles extends JViewLegacy
{
	function display( $tpl = null )
	{
		$files		= $this->get('files');
		$sermons	= $this->get('sermons');
		$this->state	= $this->get('state');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$audio_ext = array('aac', 'm4a', 'mp3');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2', 'wmv');
		$start = strlen(JPATH_SITE)+1;
		$this->items = array();
		foreach ($files as $key => $value){
			$value = substr($value, $start);
			if (in_array($value, $sermons)){
				unset($files[$key]);
				continue;
			}
			$ext = JFile::getExt($value);
			$this->items[$key]['file'] = '/'.$value;
			if(in_array($ext, $audio_ext)){$this->items[$key]['type'] = 'audio';}
			elseif(in_array($ext, $video_ext)){$this->items[$key]['type'] = 'video';}
			else{$this->items[$key]['type'] = $ext;}
		}
		parent::display($tpl);
	}
}