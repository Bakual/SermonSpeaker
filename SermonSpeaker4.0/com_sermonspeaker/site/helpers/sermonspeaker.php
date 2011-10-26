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
			$link = JURI::root().trim($path, ' /');
		}

		return $link;
	}

	function insertdlbutton($id, $type='audio') {
		$fileurl = JRoute::_('index.php?&task=download&id='.$id.'&type='.$type);
		$html = '<input id="sermon_download" class="button download_btn" type="button" value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON').'" onclick="window.location.href=\''.$fileurl.'\'" />';

		return $html;
	}

	function insertPopupButton($id = NULL, $player) {
		$html = '<input class="button popup_btn" type="button" name="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" value="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" onclick="popup=window.open(\''.JRoute::_('index.php?view=sermon&layout=popup&id='.$id.'&tmpl=component').'\', \'PopupPage\', \'height='.$player->popup['height'].',width='.$player->popup['width'].',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
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

	function insertSermonTitle($i, $item, $player){
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
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
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
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
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