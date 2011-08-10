<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Player Helper
 */
class SermonspeakerHelperPlayer {
	function insertPlayer($item, $params, $count = '1') {
		// defining some variables
		$this->item		= $item;
		$this->count	= $count;
		$this->params	= $params;
		$this->prio		= $this->params->get('fileprio', 0);

		// Rem: may be improved to a simple BOOL variable and then checked in the respective playerfunction
		if ($this->params->get('autostart') && (JRequest::getCmd('view') != 'seriessermon')){
			$this->start[0]='true'; $this->start[1]='1'; $this->start[2]='yes';
		} else {
			$this->start[0]='false'; $this->start[1]='0'; $this->start[2]='no';
		}

		// Dispatching
		if(is_array($this->item)){
			// Playlist
			return $this->SeriesPlayer();
		} else {
			// Single File
			return $this->SinglePlayer();
		}
	}

	private function SeriesPlayer(){
		$player	= JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
		$skin	= $this->params->get('jwskin', '');
		if ($skin){
			$skin = '	  skin: "'.$skin.'",';
		}
		$entries = array();
		foreach ($this->item as $temp_item){
		// Choosing the default file to play based on prio and availabilty
			if (($temp_item->audiofile && !$this->prio) || ($temp_item->audiofile && !$temp_item->videofile)){
				$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile).'"';
				$title = ', title: "'.$temp_item->sermon_title.'"';
			} elseif (($temp_item->videofile && $this->prio) || ($temp_item->videofile && !$temp_item->audiofile)){
				$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile).'"';
				$title = ', title: "'.$temp_item->sermon_title.'"';
			} else {
				$file = 'file: "'.JURI::root().'"';
				$title = ', title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
			}
			$meta = '';
			$desc = '';
			if ($temp_item->sermon_date){
				$desc[] = JText::_('JDATE').': '.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), 'UTC');
			}
			if ($temp_item->name){
				$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER').': '.$temp_item->name;
			}
			if ($desc){
				$meta .= ', description: "'.implode('<br/>', $desc).'"';
			}
			if ($temp_item->sermon_time != '00:00:00'){
				$time_arr = explode(':', $temp_item->sermon_time);
				$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
				$meta .= ', duration: '.$seconds;
			}
			if ($temp_item->picture){
				$meta .= ', image: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->picture).'"';
			} elseif ($temp_item->pic){
				$meta .= ', image: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->pic).'"';
			}
			$entries[] = '{'.$file.$title.$meta.'}';
			if ($this->params->get('fileswitch')){
				// Preparing specific playlists for audio and video
				if ($temp_item->audiofile){
					$audios[] = '{file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile).'"'.$title.$meta.'}';
				} else {
					$audios[] = '{file: "'.JURI::root().'", title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"'.$meta.'}';
				}
				$return['audio-pl'] = implode(',',$audios);
				if ($temp_item->videofile){
					$videos[] = '{file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile).'"'.$title.$meta.'}';
				} else {
					$videos[] = '{file: "'.JURI::root().'", title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"'.$meta.'}';
				}
				$return['video-pl'] = implode(',',$videos);
			}
		}
		$playlist = implode(',', $entries);
		$return['mspace'] = '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
		$return['script'] = '<script type="text/javascript">'
							.'	jwplayer("mediaspace'.$this->count.'").setup({'
							.'	  flashplayer: "'.$player.'",'
							.'	  playlist: ['
							.$playlist
							.'	  ],'
							.'	  "playlist.size": 60,'
							.'	  "playlist.position": "top",'
							.'	  autostart: '.$this->start[0].','
							.'	  controlbar: "bottom",'
							.'	  width: "100%",'
							.'	  height: 80,'
							.$skin
							.'	  events: {'
							.'		onPlaylistItem: function(event){'
							.'		  var i = 0;'
							.'		  while (document.id("sermon"+i)){'
							.'			document.id("sermon"+i).removeClass("ss-current");'
							.'			i++;'
							.'		  }'
							.'	  document.id("sermon"+event.index).addClass("ss-current");'
							.'	  }'
							.'	  }'
							.'	});'
							.'</script>';
		$return['height'] = $this->params->get('popup_height');
		$return['width']  = '380';
		$return['default-pl'] = $playlist;
		$return['status'] = 'playlist';
		
		return $return;
	}

	private function SinglePlayer(){
		// Choosing the default file to play based on prio and availablity, also check if Fileswitch is possible
		$return['switch']	= false;
		if (($this->item->audiofile && !$this->prio) || ($this->item->audiofile && !$this->item->videofile)){
			$return['file'] = SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile);
			if ($this->params->get('fileswitch') && $this->item->videofile){
				$return['audio']	= '{file: "'.$return['file'].'"}';
				$return['video']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->videofile).'"}';
				$return['switch']	= true;
			}
		} elseif (($this->item->videofile && $this->prio) || ($this->item->videofile && !$this->item->audiofile)){
			$return['file'] = SermonspeakerHelperSermonspeaker::makelink($this->item->videofile);
			if ($this->params->get('fileswitch') && $this->item->audiofile){
				$return['audio']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile).'"}';
				$return['video']	= '{file: "'.$return['file'].'"}';
				$return['switch']	= true;
			}
		} else {
			$return['file']   = '';
			$return['player'] = '';
			$return['mspace'] = '';
			$return['script'] = '';
			$return['height'] = 0;
			$return['width']  = 0;
			$return['status'] = 'error';
			$return['error']  = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
			return $return;
		}
		// Get extension of file
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($return['file']);

		if (!$this->params->get('alt_player') || ($ext != 'mp3')){
			$player = JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
			$skin	= $this->params->get('jwskin', '');
			if ($skin){
				$skin = '	  skin: "'.$skin.'",';
			}
			if ($this->item->sermon_time != '00:00:00'){
				$time_arr = explode(':', $this->item->sermon_time);
				$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
				$duration = '	  duration: '.$seconds.',';
			} else {
				$duration = '';
			}
		}

		// Declare the supported file extensions
		$audio_ext = array('aac', 'm4a', 'mp3');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2');
		if(in_array($ext, $audio_ext)){
			// Audio File
			$return['mspace'] = '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
			if ($this->params->get('alt_player') && ($ext == 'mp3')){
				$options = '';
				if ($this->item->sermon_title){
					$options .= 'titles: "'.$this->item->sermon_title.'",';
				}
				if ($this->item->speaker_name){
					$options .= 'artists: "'.$this->item->speaker_name.'",';
				}
				$return['player'] = 'PixelOut';
				$return['script'] = '<script type="text/javascript">'
									.'	AudioPlayer.embed("mediaspace'.$this->count.'", {'
									.'		soundFile: "'.urlencode($return['file']).'",'
									.'		'.$options
									.'		autostart: "'.$this->start[2].'"'
									.'	})'
									.'</script>';
				$return['switch'] = false;
			} else {
				$image = '';
				if ($this->item->picture){
					$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->picture).'",';
				} elseif ($this->item->pic){
					$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->pic).'",';
				}
				$return['player'] = 'JWPlayer';
				$return['script'] = '<script type="text/javascript">'
									.'	jwplayer("mediaspace'.$this->count.'").setup({'
									.'	  flashplayer: "'.$player.'",'
									.'	  file: "'.$return['file'].'",'
									.'	  autostart: '.$this->start[0].','
									.$duration
									.$skin
									.$image
									.'	  controlbar: "bottom",'
									.'	  width: 250,'
									.'	  height: 23'
									.'	});'
									.'</script>';
			}
			$return['height'] = $this->params->get('popup_height') + 23;
			$return['width']  = '380';
			$return['status'] = 'audio';
		} elseif(in_array($ext, $video_ext) || (strpos($return['file'], 'http://www.youtube.com') === 0)) {
			// Video File
			$image = '';
			if ($this->item->picture){
				$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->picture).'",';
			} elseif ($this->item->pic){
				$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->pic).'",';
			}
			$return['player'] = 'JWPlayer';
			$return['mspace'] = '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
			$return['script'] = '<script type="text/javascript">'
								.'	jwplayer("mediaspace'.$this->count.'").setup({'
								.'	  flashplayer: "'.$player.'",'
								.'	  file: "'.$return['file'].'",'
								.'	  autostart: '.$this->start[0].','
								.$duration
								.$skin
								.$image
								.'	  width: "'.$this->params->get('mp_width').'",'
								.'	  height: "'.$this->params->get('mp_height').'"'
								.'	});'
								.'</script>';
			$return['height'] = $this->params->get('mp_height') + $this->params->get('popup_height');
			if (strpos($this->params->get('mp_width'), '%')){
				$return['width'] = 500;
			} else {
				$return['width'] = $this->params->get('mp_width') + 130;
			}
			$return['status'] = 'video';
		} elseif($ext == 'wmv'){ // TODO: is anyone using this? Could switch to Longtail Silverlight player fpr wmv and wma support
			// WMV File
			$return['player'] = 'Windows';
			$return['mspace'] = '<object id="mediaplayer" width="400" height="323" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
								.'	<param name="filename" value="'.$return['file'].'">'
								.'	<param name="autostart" value="'.$this->start[1].'">'
								.'	<param name="transparentatstart" value="true">'
								.'	<param name="showcontrols" value="1">'
								.'	<param name="showdisplay" value="0">'
								.'	<param name="showstatusbar" value="1">'
								.'	<param name="autosize" value="1">'
								.'	<param name="animationatstart" value="false">'
								.'	<embed name="MediaPlayer" src="'.$return['file'].'" width="'.$this->params->get('mp_width').'" height="'.$this->params->get('mp_height').'" type="application/x-mplayer2" autostart="'.$this->start[1].'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
								.'	</embed>'
								.'</object>';
			$return['script'] = '';
			$return['height'] = $this->params->get('mp_height') + $this->params->get('popup_height');
			$return['width']  = $this->params->get('mp_width') + 130;
			$return['status'] = 'wmv file';
			$return['switch'] = false;
		} else {
			$return['player'] = '';
			$return['mspace'] = '';
			$return['script'] = '';
			$return['height'] = 0;
			$return['width']  = 0;
			$return['status'] = 'error';
			$return['error']  = 'Unsupported Filetype';
			$return['switch'] = false;
		}
		return $return;
	}
}