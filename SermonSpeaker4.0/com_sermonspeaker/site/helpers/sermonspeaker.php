<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Sermonspeaker Helper
 */
class SermonspeakerHelperSermonspeaker
{
	function SpeakerTooltip($id, $pic, $name) {
		if (!$pic) { 
			// check if there is no picture and set nopict.jpg
			$pic = JURI::root().'media/com_sermonspeaker/images/nopict.jpg';
		} else {
			$pic = SermonspeakerHelperSermonspeaker::makelink($pic);
		}
		$html = '<a class="modal" href="'.JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($id).'&layout=popup&tmpl=component').'" rel="{handler: \'iframe\', size: {x: 700, y: 500}}">';
		$html .= JHTML::tooltip('<img src="'.$pic.'" alt="'.$name.'">',$name,'',$name).'</a>';

		return $html;
	}
		
	function insertAddfile($addfile, $addfileDesc, $show_icon = 0) {
		if ($addfile) {
			$link = SermonspeakerHelperSermonspeaker::makelink($addfile); 
			// Show filename if no addfileDesc is set
			if (!$addfileDesc){
				$slash = strrpos($addfile, '/');
				if ($slash !== false) {
					$addfileDesc = substr($addfile, $slash + 1);
				} else {
					$addfileDesc = $addfile;
				}
			}
			$html = '';
			if($show_icon){
				// Get extension of file
				jimport('joomla.filesystem.file');
				$ext = JFile::getExt($addfile);
				if (file_exists(JPATH_SITE.DS.'media'.DS.'com_sermonspeaker'.DS.'icons'.DS.$ext.'.png')) {
					$file = JURI::root().'media/com_sermonspeaker/icons/'.$ext.'.png';
				} else {
					$file = JURI::root().'media/com_sermonspeaker/icons/icon.png';
				}
				$html .= '<a title="'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" target="_blank"><img src="'.$file.'" width="18" height="20" alt="" /></a>&nbsp;';
			}
			$html .= '<a title="'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" target="_blank">'.$addfileDesc.'</a>';

			return $html;
		} else {
			return;
		}
	}
	
	function makelink($path) {
		if (substr($path, 0, 7) == 'http://'){
			$link = $path;
		} else {
			if (substr($path, 0, 1) == '/') {
				$path = substr($path, 1);
			}
			$link = JURI::root().$path;
		}

		return $link;
	}

	function insertdlbutton($id, $path) {
		//Check if link targets to an external source
		if (substr($path, 0, 7) == 'http://' && (strpos($path, JURI::root()) !== 0)){ //File is external
			$fileurl = $path;
		} else { //File is locally
			$fileurl = JRoute::_('index.php?&task=download&id='.$id);
		}
		$html = '<input class="button download_btn" type="button" value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON').'" onclick="window.location.href=\''.$fileurl.'\'" />';

		return $html;
	}
	
	function insertPopupButton($id = NULL, $player) {
		$html = '<input class="button popup_btn" type="button" name="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" value="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" onclick="popup=window.open(\''.JRoute::_('index.php?view=sermon&layout=popup&id='.$id.'&tmpl=component').'\', \'PopupPage\', \'height='.$player['height'].',width='.$player['width'].',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}
	
	function insertPlayer($item, $artist = '', $count = '1') {
		$prio		= $this->params->get('fileprio', 0);

		$view = JRequest::getCmd('view');
		if ($this->params->get('autostart') && ($view != 'seriessermon')){
			$start[0]='true'; $start[1]='1'; $start[2]='yes';
		} else {
			$start[0]='false'; $start[1]='0'; $start[2]='no';
		}
		if(($view == 'serie') || ($view == 'sermons') || ($view == 'archive') || ($view == 'speaker')){
			// Playlist
			$player = JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
			$skin	= $this->params->get('jwskin', '');
			if ($skin){
				$skin = '	  skin: "'.$skin.'",';
			}
			$entries = array();
			foreach ($item as $temp_item){
				// Choosing the default file to play based on prio and availabilty
				if (($temp_item->audiofile && !$prio) || ($temp_item->audiofile && !$temp_item->videofile)){
					$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile).'"';
					$title = ', title: "'.$temp_item->sermon_title.'"';
				} elseif (($temp_item->videofile && $prio) || ($temp_item->videofile && !$temp_item->audiofile)){
					$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile).'"';
					$title = ', title: "'.$temp_item->sermon_title.'"';
				} else {
					$file = 'file: "'.JURI::root().'"';
					$title = ', title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
				}
				$meta = '';
				if ($temp_item->sermon_time != '00:00:00'){
					$time_arr = explode(':', $temp_item->sermon_time);
					$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$meta .= ', duration: '.$seconds;
				}
				if ($temp_item->sermon_date){
					$meta .= ', description: "'.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), 'UTC').'"';
				}
				if ($temp_item->picture){
					$meta .= ', image: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->picture).'"';
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
			$return['mspace'] = '<div id="mediaspace'.$count.'" style="text-align:center;">Flashplayer needs Javascript turned on</div>';
			$return['script'] = '<script type="text/javascript">'
								.'	jwplayer("mediaspace'.$count.'").setup({'
								.'	  flashplayer: "'.$player.'",'
								.'	  playlist: ['
								.$playlist
								.'	  ],'
								.'	  "playlist.size": 60,'
								.'	  "playlist.position": "top",'
								.'	  autostart: '.$start[0].','
								.'	  controlbar: "bottom",'
								.'	  width: "80%",'
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
		} else {
			// Single File
			// Choosing the default file to play based on prio and availabilty
			if (($item->audiofile && !$prio) || ($item->audiofile && !$item->videofile)){
				$return['file'] = SermonspeakerHelperSermonspeaker::makelink($item->audiofile);
			} elseif (($item->videofile && $prio) || ($item->videofile && !$item->audiofile)){
				$return['file'] = SermonspeakerHelperSermonspeaker::makelink($item->videofile);
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
				if ($item->sermon_time != '00:00:00'){
					$time_arr = explode(':', $item->sermon_time);
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
				$return['mspace'] = '<div id="mediaspace'.$count.'">Flashplayer needs Javascript turned on</div>';
				if ($this->params->get('alt_player') && ($ext == 'mp3')){
					$options = '';
					if ($item->sermon_title){
						$options .= 'titles: "'.$item->sermon_title.'",';
					}
					if ($artist){
						$options .= 'artists: "'.$artist.'",';
					}
					$return['player'] = 'PixelOut';
					$return['script'] = '<script type="text/javascript">'
										.'	AudioPlayer.embed("mediaspace'.$count.'", {'
										.'		soundFile: "'.urlencode($return['file']).'",'
										.'		'.$options
										.'		autostart: "'.$start[2].'"'
										.'	})'
										.'</script>';
				} else {
					$return['player'] = 'JWPlayer';
					$return['script'] = '<script type="text/javascript">'
										.'	jwplayer("mediaspace'.$count.'").setup({'
										.'	  flashplayer: "'.$player.'",'
										.'	  file: "'.$return['file'].'",'
										.'	  autostart: '.$start[0].','
										.$duration
										.$skin
										.'	  controlbar: "bottom",'
										.'	  width: 250,'
										.'	  height: 23'
										.'	});'
										.'</script>';
				}
				$return['height'] = $this->params->get('popup_height');
				$return['width']  = '380';
				$return['status'] = 'audio';
			} elseif(in_array($ext, $video_ext)) {
				// Video File
				$return['player'] = 'JWPlayer';
				$return['mspace'] = '<div id="mediaspace'.$count.'">Flashplayer needs Javascript turned on</div>';
				$return['script'] = '<script type="text/javascript">'
									.'	jwplayer("mediaspace'.$count.'").setup({'
									.'	  flashplayer: "'.$player.'",'
									.'	  file: "'.$return['file'].'",'
									.'	  autostart: '.$start[0].','
									.$duration
									.$skin
									.'	  width: '.$this->params->get('mp_width').','
									.'	  height: '.$this->params->get('mp_height')
									.'	});'
									.'</script>';
				$return['height'] = $this->params->get('mp_height') + $this->params->get('popup_height');
				$return['width']  = $this->params->get('mp_width') + 130;
				$return['status'] = 'video';
			} elseif($ext == 'wmv'){ // TODO: is anyone using this? Could switch to Longtail Silverlight player fpr wmv and wma support
				// WMV File
				$return['player'] = 'Windows';
				$return['mspace'] = '<object id="mediaplayer" width="400" height="323" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
									.'	<param name="filename" value="'.$return['file'].'">'
									.'	<param name="autostart" value="'.$start[1].'">'
									.'	<param name="transparentatstart" value="true">'
									.'	<param name="showcontrols" value="1">'
									.'	<param name="showdisplay" value="0">'
									.'	<param name="showstatusbar" value="1">'
									.'	<param name="autosize" value="1">'
									.'	<param name="animationatstart" value="false">'
									.'	<embed name="MediaPlayer" src="'.$return['file'].'" width="'.$this->params->get('mp_width').'" height="'.$this->params->get('mp_height').'" type="application/x-mplayer2" autostart="'.$start[1].'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
									.'	</embed>'
									.'</object>';
				$return['script'] = '';
				$return['height'] = $this->params->get('mp_height') + $this->params->get('popup_height');
				$return['width']  = $this->params->get('mp_width') + 130;
				$return['status'] = 'wmv file';
			} else {
				$return['player'] = '';
				$return['mspace'] = '';
				$return['script'] = '';
				$return['height'] = 0;
				$return['width']  = 0;
				$return['status'] = 'error';
				$return['error']  = 'Unsupported Filetype';
			}
		}

		return $return;
	}

	function insertTime($time) {
		$tmp = explode(':', $time);
		if ($tmp[0] == 0) {
			$html = $tmp[1].':'.$tmp[2];
		} else {
			$html = $tmp[0].':'.$tmp[1].':'.$tmp[2];
		}

		return $html;
	}

	function fu_logoffbtn () {
		$html 	= '<form>'
				. '<input type="button" value="'.JText::_('JLOGOUT').'" onclick="window.location.href=\''.JRoute::_('index.php?option=com_users&task=user.logout').'\'">'
				. '</form>';
		return $html;
	}
	
	function insertSermonTitle($i, $item){
		$return = '';
		// Prepare play icon function
		$options = array();
		switch ($this->params->get('list_icon_function', 3)){
			case 0:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
				$return .= JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $pic);
				break;
			case 1:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
				$return .= JHTML::Link(SermonspeakerHelperSermonspeaker::makelink($item->audiofile), $pic);
				break;
			case 2:
				$options['onclick'] = 'jwplayer().playlistItem('.$i.')';
				$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
				$options['class'] = 'icon_play pointer';
				$return .= JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'), $options);
				break;
			case 3:
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$this->params->get('popup_height').',width='.$this->params->get('mp_width').",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$options['class'] = 'icon_play pointer';
				$return .= JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_POPUPPLAYER'), $options);
				break;
		}
		$return .= ' ';
		// Prepare title link function
		$options = array();
		switch ($this->params->get('list_title_function', 0)){
			case 0:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$return .= JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
				break;
			case 1:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
				$return .= JHTML::Link(SermonspeakerHelperSermonspeaker::makelink($item->audiofile), $item->sermon_title, $options);
				break;
			case 2:
				$options['onclick'] = 'jwplayer().playlistItem('.$i.')';
				$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
				$return .= JHTML::Link('#', $item->sermon_title, $options);
				break;
			case 3:
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$this->params->get('popup_height').',width='.$this->params->get('mp_width').",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$return .= JHTML::Link('#', $item->sermon_title, $options);
				break;
		}
		return $return;
	}
	
	function insertSearchTags($metakey){
		// Code from Douglas Machado
		$links = array();
		$keywords = explode(',', $metakey);
		foreach($keywords as $keyword){
			$keyword = trim($keyword);
			if ($keyword){
				$links[] = '<a href="'.JRoute::_('index.php?option=com_search&ordering=newest&searchphrase=all&searchword='.$keyword).'" >'.$keyword.'</a>';
			}
		}
		return implode(', ', $links);
	}
}