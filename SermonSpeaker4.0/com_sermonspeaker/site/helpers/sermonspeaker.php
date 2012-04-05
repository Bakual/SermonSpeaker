<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Sermonspeaker Helper
 */
class SermonspeakerHelperSermonspeaker
{
	private static $params;
	private static $view;

	static function getParams() {
		$app = JFactory::getApplication();
		self::$params	= $app->getParams('com_sermonspeaker');
	}

	static function getView() {
		self::$view	= JRequest::getCmd('view', 'sermons');
	}

	static function SpeakerTooltip($id, $pic, $name) {
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

	static function insertAddfile($addfile, $addfileDesc, $show_icon = 0) {
		if ($addfile) {
			$link = SermonspeakerHelperSermonspeaker::makelink($addfile); 
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
			if($show_icon != 2){
				// Show filename if no addfileDesc is set
				if (!$addfileDesc){
					$slash = strrpos($addfile, '/');
					if ($slash !== false) {
						$addfileDesc = substr($addfile, $slash + 1);
					} else {
						$addfileDesc = $addfile;
					}
				}
				$html .= '<a title="'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" target="_blank">'.$addfileDesc.'</a>';
			}
			return $html;
		} else {
			return;
		}
	}

	static function makelink($path) {
		if (strpos($path, 'http://') === 0){
			$link = $path;
		} else {
			$link = JURI::root().trim($path, ' /');
		}

		return $link;
	}

	static function insertdlbutton($id, $type='audio', $mode='0') {
		$fileurl = JRoute::_('index.php?task=download&id='.$id.'&type='.$type);
		if ($mode){
			$html = '<a href="'.$fileurl.'" target="_new" title="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_'.$type).'">'
						.'<img src="media/com_sermonspeaker/images/download.png" alt="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_'.$type).'" />'
					.'</a>';
		} else {
			$html = '<input id="sermon_download" class="button download_btn" type="button" value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_'.$type).'" onclick="window.location.href=\''.$fileurl.'\'" />';
		}

		return $html;
	}

	static function insertPopupButton($id = NULL, $player) {
		$html = '<input class="button popup_btn" type="button" name="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" value="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" onclick="popup=window.open(\''.JRoute::_('index.php?view=sermon&layout=popup&id='.$id.'&tmpl=component').'\', \'PopupPage\', \'height='.$player->popup['height'].',width='.$player->popup['width'].',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}

	static function insertTime($time) {
		$tmp = explode(':', $time);
		if ($tmp[0] == 0) {
			$html = $tmp[1].':'.$tmp[2];
		} else {
			$html = $tmp[0].':'.$tmp[1].':'.$tmp[2];
		}

		return $html;
	}

	static function fu_logoffbtn () {
		$html 	= '<form>'
				. '<input type="button" value="'.JText::_('JLOGOUT').'" onclick="window.location.href=\''.JRoute::_('index.php?option=com_users&task=user.logout').'\'">'
				. '</form>';
		return $html;
	}

	static function insertSermonTitle($i, $item, $player){
		if(!self::$params){
			self::getParams();
		}
		if(!self::$view){
			self::getView();
		}
		$return = '';
		// Prepare play icon function
		$options = array();
		switch (self::$params->get('list_icon_function', 3)){
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
				if(in_array(self::$view.':player', self::$params->get('col'))){
					$options['onclick'] = 'ss_play('.$i.')';
					$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$options['class'] = 'icon_play pointer';
					$return .= JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'), $options);
				}
				break;
			case 3:
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$options['class'] = 'icon_play pointer';
				$return .= JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_POPUPPLAYER'), $options);
				break;
			case 4:
				break;
		}
		$return .= ' ';
		// Prepare title link function
		$options = array();
		switch (self::$params->get('list_title_function', 0)){
			case 0:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$return .= JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
				break;
			case 1:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$return .= JHTML::Link(SermonspeakerHelperSermonspeaker::makelink($item->audiofile), $item->sermon_title, $options);
				break;
			case 2:
				if(in_array(self::$view.':player', self::$params->get('col'))){
					$options['onclick'] = 'ss_play('.$i.')';
					$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$return .= JHTML::Link('#', $item->sermon_title, $options);
				} else {
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$return .= JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
				}
				break;
			case 3:
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$return .= JHTML::Link('#', $item->sermon_title, $options);
				break;
		}
		return $return;
	}

	static function insertSearchTags($metakey){
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

	static function insertScriptures($scripture, $between, $addTag = true){
		if(!$scripture){
			return;
		}
		$explode = explode('!', $scripture);
		$scriptures = array();
		foreach ($explode as $passage){
			$scriptures[] = self::buildScripture($passage, $addTag);
		}
		return implode($between, $scriptures);
	}

	static function buildScripture($scripture, $addTag = true){
		if(!self::$params){
			self::getParams();
		}
		$explode	= explode('|',$scripture);
		$text = '';
		if ($explode[5]){
			$text .= $explode[5];
		} else {
			$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
			$text .= JText::_('COM_SERMONSPEAKER_BOOK_'.$explode[0]);
			if ($explode[1]){
				$text .= ' '.$explode[1];
				if ($explode[2]){
					$text .= $separator.$explode[2];
				}
				if ($explode[3] || $explode[4]){
					$text .= '-';
					if ($explode[3]){
						$text .= $explode[3];
						if ($explode[4]){
							$text .= $separator.$explode[4];
						}
					} else {
						$text .= $explode[4];
					}
				}
			}
			if($text && $addTag){
				$tags = self::$params->get('plugin_tag');
				$text = $tags[0].$text.$tags[1];
			}
		}
		return $text;
	}

	static function getMime($ext){
		switch ($ext){
			case 'mp3':
				$mime	= 'audio/mpeg';
				break;
			case 'aac':
				$mime	= 'audio/aac';
				break;
			case 'm4a':
				$mime	= 'audio/mp4a-latm';
				break;
			case 'flv':
				$mime	= 'video/x-flv';
				break;
			case 'mp4':
			case 'f4v':
				$mime	= 'video/mp4';
				break;
			case 'm4v':
				$mime	= 'video/m4v';
				break;
			case 'mov':
				$mime	= 'video/quicktime';
				break;
			case '3gp':
				$mime	= 'video/3gpp';
				break;
			case '3g2':
				$mime	= 'video/3gpp2';
				break;
			case 'pdf':
				$mime	= 'application/pdf';
				break;
			default:
				$mime	= 'video/mp4';
				break;
		}
		return $mime;
	}
}