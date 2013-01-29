<?php
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_SITE.'/helpers/player.php');
/**
 * JW Player 5
 */
class SermonspeakerHelperPlayerFlowplayer3 extends SermonspeakerHelperPlayer {
	public function isSupported($file){
		$ext		= JFile::getExt($file);
		$audio_ext	= array('mp3');
		$video_ext	= array('mp4', 'f4v', 'flv');
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
		$this->player	= 'FlowPlayer';
		$this->toggle	= $this->params->get('fileswitch', 0);

		$player			= JURI::base(true).'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.11.swf';
		// Load plugins
		$showplaylist			= (is_array($item)) ? 'true' : 'false';
		$plugins['audio']		= "{url:'flowplayer.audio-3.2.9.swf'}";
		$plugins['controls']	= "{url:'flowplayer.controls-3.2.11.swf',fullscreen:true,height:23,autoHide:false,playlist:".$showplaylist."}";
		if ($gaid = $this->params->get('ga_id', ''))
		{
			$plugins['gatracker'] = "{url:'flowplayer.analytics-3.2.8.swf',accountId:'".$gaid."'}";
		}
		if ($this->params->get('share', 0))
		{
			$plugins['sharing'] = "{url:'flowplayer.sharing-3.2.8.swf'}";
		}
		if ($this->params->get('viral', 0))
		{
			$plugins['sharing'] = "{url:'flowplayer.viralvideos-3.2.10.swf'}";
		}
		foreach ($plugins as $key => $value)
		{
			$plugins[$key] = $key.':'.$value;
		}
		$options['plugins'] = '{'.implode(',', $plugins).'}';
		// Generic options
		$autostart = $this->config['autostart'] ? 'true' : 'false';
		$options['clip'] = "{scaling:'fit',image:false,autoBuffering:true,autoPlay:".$autostart.",eventCategory:'SermonSpeaker'}";

		if (is_array($item))
		{
			$options['onStart'] = 'function(){'
					.'var i = 0;'
					.'while (document.id("sermon"+i)){'
						.'document.id("sermon"+i).removeClass("ss-current");'
							.'i++;'
						.'}'
					.'entry = flowplayer().getClip();'
					.'document.id("sermon"+entry.index).addClass("ss-current");'
					.'if (entry.duration > 0){'
						.'time = new Array();'
						.'var hrs = Math.floor(entry.duration/3600);'
						.'if (hrs > 0){time.push(hrs);}'
						.'var min = Math.floor((entry.duration - hrs * 3600)/60);'
						.'if (hrs > 0 && min < 10){'
							.'time.push("0" + min);'
						.'} else {'
							.'time.push(min);'
						.'}'
						.'var sec = entry.duration - hrs * 3600 - min * 60;'
						.'if (sec < 10){'
							.'time.push("0" + sec);'
						.'} else {'
							.'time.push(sec);'
						.'}'
						.'var duration = time.join(":");'
						.'document.id("playing-duration").innerHTML = duration;'
					.'} else {'
						.'document.id("playing-duration").innerHTML = "";'
					.'}'
					.'document.id("playing-pic").src = entry.coverImage;'
					.'if(entry.coverImage){'
						.'document.id("playing-pic").style.display = "block";'
					.'}else{'
						.'document.id("playing-pic").style.display = "none";'
					.'}'
					.'if(entry.error){'
						.'document.id("playing-error").innerHTML = entry.error;'
						.'document.id("playing-error").style.display = "block";'
					.'}else{'
						.'document.id("playing-error").style.display = "none";'
					.'}'
					.'document.id("playing-title").innerHTML = entry.title;'
					.'document.id("playing-desc").innerHTML = entry.description;'
				.'}';
			$this->toggle = $this->params->get('fileswitch', 0);
			$this->setDimensions('23px', '100%');
			$type = ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->prio)) ? 'a' : 'v';
			$entries = array();
			foreach ($item as $temp_item)
			{
				$entry = array();
				// Choose picture to show
				$img = SermonspeakerHelperSermonspeaker::insertPicture($temp_item, 1);
				// Choosing the default file to play based on prio and availabilty
				if (($this->config['type'] != 'video') && ($temp_item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$temp_item->videofile)))
				{
					$entry['url']			= SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile);
					$entry['eventCategory']	= 'SermonSpeaker/Audio';
				} 
				elseif (($this->config['type'] != 'audio') && ($temp_item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$temp_item->audiofile)))
				{
					$entry['url']	= SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile);
					$entry['eventCategory']	= 'SermonSpeaker/Video';
				}
				else
				{
					$entry['url']	= ($img) ? $img : JURI::base(true).'/media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
					$entry['error']	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
				}
				$entry['title']	= urlencode(addslashes($temp_item->sermon_title));
				if ($temp_item->sermon_time != '00:00:00'){
					$time_arr	= explode(':', $temp_item->sermon_time);
					$seconds	= ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$entry['duration']	= $seconds;
				}
				if ($img){
					$entry['coverImage'] = $img;
				}
				$desc = array();
				if ($temp_item->sermon_date){
					$desc[] = JText::_('JDATE').': '.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), true);
				}
				if ($temp_item->name){
					$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER').': '.urlencode(addslashes($temp_item->name));
				}
				$entry['description']	= implode('<br/>', $desc);
				foreach ($entry as $key => $value)
				{
					$entry[$key] = $key.":'".$value."'";
				}
				$entries[] = implode(',', $entry);

				if ($this->toggle)
				{
					// Preparing specific playlists for audio and video
					if ($temp_item->audiofile)
					{
						$file = SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile);
						$entry['eventCategory']	= "eventCategory:'SermonSpeaker/Audio'";
						unset($entry['error']);
					}
					else
					{
						$file = ($img) ? $img : JURI::base(true).'/media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
						$entry['error'] = "error:'".JText::_('JGLOBAL_RESOURCE_NOT_FOUND')."'";
						unset($entry['eventCategory']);
					}
					$entry['url'] = "url:'".$file."'";
					$audios[] = implode(',', $entry);
					if ($temp_item->videofile)
					{
						$file = SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile);
						$entry['eventCategory']	= "eventCategory:'SermonSpeaker/Video'";
						unset($entry['error']);
					}
					else 
					{
						$file = ($img) ? $img : JURI::base(true).'/media/com_sermonspeaker/images/'.$this->params->get('defaultpic', 'nopict.jpg');
						$entry['error']	= "error:'".JText::_('JGLOBAL_RESOURCE_NOT_FOUND')."'";
						unset($entry['eventCategory']);
					}
					$entry['url'] = "url:'".$file."'";
					$videos[] = implode(',', $entry);
				}
			}
			$this->playlist['default'] = implode('},{', $entries);
			if ($this->toggle)
			{
				$this->playlist['audio'] = '{'.implode('},{', $audios).'}';
				$this->playlist['video'] = '{'.implode('},{', $videos).'}';
			}
		}
		else
		{
			$type	= ($this->mode == 'audio') ? 'a' : 'v';
			$cat	= ($this->mode == 'audio') ? 'Audio' : 'Video';
			$this->playlist['default'] = "url:'".$this->file."',eventCategory:'SermonSpeaker/".$cat."'";
			if ($this->toggle)
			{
				$this->playlist['audio'] = "{url:'".$this->playlist['audio']."',eventCategory:'SermonSpeaker/Audio'}";
				$this->playlist['video'] = "{url:'".$this->playlist['video']."',eventCategory:'SermonSpeaker/Video'}";
			}
		}
		foreach ($options as $key => $value)
		{
			$options[$key] = $key.':'.$value;
		}
		$this->mspace = '<div style="width:'.$this->config[$type.'width'].'; height:'.$this->config[$type.'height'].'" id="mediaspace'.$this->config['count'].'"></div>';
		$this->script = '<script type="text/javascript">'
							.'flowplayer("mediaspace'.$this->config['count'].'", "'.$player.'", {'
								.'playlist: [{'
									.$this->playlist['default']
								.'}],'
								.implode(',', $options)
							.'});'
						.'</script>';

		// Loading needed Javascript only once
		if (!self::$fwscript)
		{
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('function ss_play(id){flowplayer().play(parseInt(id));}');
			JHTML::Script('media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.10.min.js');
			if ($this->toggle)
			{
				$awidth		= is_numeric($this->config['awidth']) ? $this->config['awidth'].'px' : $this->config['awidth'];
				$aheight	= is_numeric($this->config['aheight']) ? $this->config['aheight'].'px' : $this->config['aheight'];
				$vwidth		= is_numeric($this->config['vwidth']) ? $this->config['vwidth'].'px' : $this->config['vwidth'];
				$vheight	= is_numeric($this->config['vheight']) ? $this->config['vheight'].'px' : $this->config['vheight'];
				if (!is_array($item))
				{
					$url = 'index.php?&task=download&id='.$item->slug.'&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'video').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO').'"';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'audio').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO').'"';
				}
				else
				{
					$download_video = '';
					$download_audio = '';
				}
				$doc->addScriptDeclaration('
					function Video() {
						flowplayer().play(['.$this->playlist['video'].']);
						document.getElementById("mediaspace'.$this->config['count'].'").style.width="'.$vwidth.'";
						document.getElementById("mediaspace'.$this->config['count'].'").style.height="'.$vheight.'";
						'.$download_video.'
					}
				');
				$doc->addScriptDeclaration('
					function Audio() {
						flowplayer().play(['.$this->playlist['audio'].']);
						document.getElementById("mediaspace'.$this->config['count'].'").style.width="'.$awidth.'";
						document.getElementById("mediaspace'.$this->config['count'].'").style.height="'.$aheight.'";
						'.$download_audio.'
					}
				');
			}
			self::$fwscript = 1;
		}
		return;
	}
}