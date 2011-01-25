<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Sermonspeaker Helper
 */
class SermonspeakerHelperSermonspeaker
{
	function SpeakerTooltip($id, $pic, $name) {
		if ($id){
			if (!$pic) { 
				// check if there is no picture and set nopict.jpg
				$pic = JURI::root().'components/com_sermonspeaker/images/nopict.jpg';
			} else {
				$pic = SermonspeakerHelperSermonspeaker::makelink($pic);
			}
			$html = '<a class="modal" href="'.JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($id).'&layout=popup&tmpl=component').'" rel="{handler: \'iframe\', size: {x: 700, y: 500}}">';
			$html .= JHTML::tooltip('<img src="'.$pic.'" alt="'.$name.'">',$name,'',$name).'</a>';
		} else {
			$html = JText::_('COM_SERMONSPEAKER_NO_SPEAKER');
		}

		return $html;
	}
		
	function insertAddfile($addfile, $addfileDesc) {
		if ($addfile) {
			$app		= JFactory::getApplication();
			$params		= $app->getParams();

			$path = $params->get('path');

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

			$dot = strrpos($addfile, '.') + 1;
			$filetype = substr($addfile, $dot);
			if (file_exists(JPATH_COMPONENT.DS.'icons'.DS.$filetype.'.png')) {
				$file = JURI::root().'components/com_sermonspeaker/icons/'.$filetype.'.png';
			} else {
				$file = JURI::root().'components/com_sermonspeaker/icons/icon.png';
			}
			$html = '<a title="'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" target="_blank"><img src="'.$file.'" width="18" height="20" alt="" /></a>&nbsp;<a title="'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" target="_blank">'.$addfileDesc.'</a>';

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
		if (substr($path, 0, 7) == 'http://'){ //File is external
			$fileurl = $path;
		} else { //File is locally
			$fileurl = JURI::root().'index.php?option=com_sermonspeaker&amp;task=download&amp;id='.$id;
		}
		$html = '<input class="button download_btn" type="button" value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON').'" onclick="window.location.href=\''.$fileurl.'\'" />';

		return $html;
	}
	
	function insertPopupButton($id = NULL, $player) {
		$html = '<input class="button popup_btn" type="button" name="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" value="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" onclick="popup=window.open(\''.JRoute::_('index.php?view=sermon&layout=popup&id='.$id.'&tmpl=component').'\', \'PopupPage\', \'height='.$player['height'].',width='.$player['width'].',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}
	
	function insertPlayer($lnk, $time = NULL, $count = '1', $title = NULL, $artist = NULL) {
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$view = JRequest::getCmd('view');
		if ($params->get('autostart') == '1' && $view != 'seriessermon') {
			$start[0]='true'; $start[1]='1'; $start[2]='yes';
		} else {
			$start[0]='false'; $start[1]='0'; $start[2]='no';
		}
		if(is_array($lnk)){
			// Playlist
			$player = JURI::root().'components/com_sermonspeaker/media/player/jwplayer/player.swf';
			
			foreach ($lnk as $item){
				$entry = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($item->sermon_path).'"';
				$entry .= ', title: "'.$item->sermon_title.'"';
				if ($item->sermon_time){
					$time_arr = explode(':', $item->sermon_time);
					$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$entry .= ', duration: '.$seconds;
				}
				if ($item->sermon_date){
					$entry .= ', description: "'.JHTML::date($item->sermon_date, JText::_($this->params->get('date_format'))).'"';
				}
				$entries[] = '{'.$entry.'}';
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
								.'	  height: 80'
								.'	});'
								.'</script>';
			$return['height'] = $params->get('popup_height');
			$return['width']  = '380';
		} else {
			// Single File

			// Get extension of file
			jimport('joomla.filesystem.file');
			$ext = JFile::getExt($lnk);

			if ($params->get('alt_player') && ($ext == 'mp3')){
				$player = JURI::root().'components/com_sermonspeaker/media/player/audio_player/player.swf';
				$options = NULL;
				if ($title){
					$options = 'titles: "'.$title.'",';
				}
				if ($artist){
					$options .= 'artists: "'.$artist.'",';
				}
			} else {
				$player = JURI::root().'components/com_sermonspeaker/media/player/jwplayer/player.swf';
				if ($time){
					$time_arr = explode(':', $time);
					$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$duration = '	  duration: '.$seconds.',';
				} else {
					$duration = '';
				}
			}

			// Declare the supported file extensions
			$audio_ext = array('aac', 'm4a', 'mp3');
			$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2');
			if(in_array($ext, $audio_ext)) {
				// Audio File
				$return['mspace'] = '<div id="mediaspace'.$count.'">Flashplayer needs Javascript turned on</div>';
				if ($params->get('alt_player') && ($ext == 'mp3')){
					$return['script'] = '<script type="text/javascript">'
										.'	AudioPlayer.embed("mediaspace'.$count.'", {'
										.'		soundFile: "'.urlencode($lnk).'",'
										.'		'.$options
										.'		autostart: "'.$start[2].'"'
										.'	})'
										.'</script>';
				} else {
					$return['script'] = '<script type="text/javascript">'
										.'	jwplayer("mediaspace'.$count.'").setup({'
										.'	  flashplayer: "'.$player.'",'
										.'	  file: "'.$lnk.'",'
										.'	  autostart: '.$start[0].','
										.$duration
										.'	  controlbar: "bottom",'
										.'	  width: 250,'
										.'	  height: 23'
										.'	});'
										.'</script>';
				}
				$return['height'] = $params->get('popup_height');
				$return['width']  = '380';
			} elseif(in_array($ext, $video_ext)) {
				// Video File
				$return['mspace'] = '<div id="mediaspace'.$count.'">Flashplayer needs Javascript turned on</div>';
				$return['script'] = '<script type="text/javascript">'
									.'	jwplayer("mediaspace'.$count.'").setup({'
									.'	  flashplayer: "'.$player.'",'
									.'	  file: "'.$lnk.'",'
									.'	  autostart: '.$start[0].','
									.$duration
									.'	  width: '.$params->get('mp_width').','
									.'	  height: '.$params->get('mp_height')
									.'	});'
									.'</script>';
				$return['height'] = $params->get('mp_height') + 100 + $params->get('popup_height');
				$return['width']  = $params->get('mp_width') + 130;
			} elseif($ext == 'wmv'){ // TODO: is anyone using this? Could switch to Longtail Silverlight player fpr wmv and wma support
				// WMV File
				$return['mspace'] = '<object id="mediaplayer" width="400" height="323" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
									.'	<param name="filename" value="'.$lnk.'">'
									.'	<param name="autostart" value="'.$start[1].'">'
									.'	<param name="transparentatstart" value="true">'
									.'	<param name="showcontrols" value="1">'
									.'	<param name="showdisplay" value="0">'
									.'	<param name="showstatusbar" value="1">'
									.'	<param name="autosize" value="1">'
									.'	<param name="animationatstart" value="false">'
									.'	<embed name="MediaPlayer" src="'.$lnk.'" width="'.$params->get('mp_width').'" height="'.$params->get('mp_height').'" type="application/x-mplayer2" autostart="'.$start[1].'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
									.'	</embed>'
									.'</object>';
				$return['script'] = NULL;
				$return['height'] = $params->get('mp_height') + 100 + $params->get('popup_height');
				$return['width']  = $params->get('mp_width') + 130;
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
}