<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Sermonspeaker Helper
 */
class SermonspeakerHelperSermonspeaker
{
	function SpeakerTooltip($id, $pic, $name) {
		if ($pic == "") { // check if there is no picture and set nopict.jpg
			$pic = JURI::root().'components/com_sermonspeaker/images/nopict.jpg';
		} elseif (substr($pic,0,7) != "http://"){ // check if the picture is locally and add the Root to it
			$pic = JURI::root().$pic;
		}
		$speaker = '<a class="modal" href="'.JRoute::_('index.php?view=speaker&layout=popup&id='.$id.'&tmpl=component').'" rel="{handler: \'iframe\', size: {x: 700, y: 500}}">';
		$speaker .= JHTML::tooltip('<img src="'.$pic.'" alt="'.$name.'">',$name,'',$name).'</a>';
		
		return $speaker;
	}
		
	function insertAddfile($addfile, $addfileDesc) {
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$path = $params->get('path');

		//Check if link targets to an external source
		if (substr($addfile,0,7) == "http://"){
			$link = $addfile;
		} else {
			$link = SermonspeakerHelperSermonspeaker::makelink($addfile); 
		}
		$filetype = trim(strrchr($addfile,'.'),'.');
		if (file_exists(JPATH_COMPONENT.DS.'icons'.DS.$filetype.'.png')) {
			$file = JURI::root().'components/com_sermonspeaker/icons/'.$filetype.'.png';
		} else {
			$file = JURI::root().'components/com_sermonspeaker/icons/icon.png';
		}
		if ($addfile) {
			$return = '<a title="'.JText::_('ADDFILE_HOOVER_TAG').'" href="'.$link.'" target="_blank"><img src="'.$file.'" width="18" height="20" alt="" /></a>&nbsp;<a title="'.JText::_('ADDFILE_HOOVER_TAG').'" href="'.$link.'" target="_blank">'.$addfileDesc.'</a>';

		return $return;
		} else { 
		return(NULL);
		}
	}
	
	function makelink($path) {
		$base = JURI::root();
		if (substr($path,0,1) == "/" ) {
		$path = substr($path,1);
		}
		$link = $base.$path;

		return $link;
	}

	function insertdlbutton($id, $path ) {
		//Check if link targets to an external source
		if (substr($path,0,7) == "http://"){
			//File is external
			$return = "<td><button class=\"download_btn\" type=\"button\" onclick=\"location='".$path."'\">".JText::_('SEARCH_BOX_SINGLESERMON')."</button></td>";
		} else { 
			//File is locally
			$fileurl = JURI::root()."index.php?option=com_sermonspeaker&task=download&id=$id";
			$return = "<td><form><input class=\"download_btn\" type=\"button\" value=\"".JText::_('SEARCH_BOX_SINGLESERMON')."\" onclick=\"window.location.href='".$fileurl."'\"> </form></td>";
		}

		return $return;
	}
	
	function insertPlayer($lnk) {
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$view = JRequest::getCmd('view');
		if ($params->get('autostart') == "1" && $view != "seriessermon") {$start="true"; $startwmp="1";} else {$start="false"; $startwmp="0";}
		if ($params->get('ga')) { $callback = "&callback=".$params->get('ga'); }
		$player = JURI::root()."components/com_sermonspeaker/media/player/player.swf";

		if((strcasecmp(substr($lnk,-4),".mp3") == 0) OR (strcasecmp(substr($lnk,-4),".m4a") == 0)) { ?>
			<!-- Embed eingepackt in Object-Tag fuer Internet Explorer -->
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="250" height="20" id="player1" name="player1">
				<param name="movie" value="<?php echo $player; ?>"/>
				<param name="wmode" value="transparent"/>
				<param name="allowfullscreen" value="true"/>
				<param name="allowscriptaccess" value="always"/>
				<param name="flashvars" value="file=<?php echo $lnk; ?>&amp;autostart=<?php echo $start; ?>&amp;height=20&amp;width=200<?php echo $callback; ?>"/>
				<embed src="<?php echo $player; ?>"
					width="250"
					height="20"
					wmode="transparent"
					allowscriptaccess="always"
					allowfullscreen="true"
					flashvars="file=<?php echo $lnk; ?>&amp;autostart=<?php echo $start; ?>&amp;height=20&amp;width=200<?php echo $callback; ?>"
				/>
			</object>
			<?php
			$pp_h = $params->get('popup_height');
			$pp_w = 380;
		} //mp3

		if((strcasecmp(substr($lnk,-4),".flv") == 0) OR (strcasecmp(substr($lnk,-4),".mp4") == 0) OR (strcasecmp(substr($lnk,-4),".m4v") == 0)) { ?>
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" <?php echo "width=\"".$params->get('mp_width')."\" height=\"".$params->get('mp_height'); ?> id="player1" name="player1">
				<param name="movie" value="<?php echo $player; ?>"/>
					<param name="wmode" value="transparent"/>
					<param name="allowfullscreen" value="true"/>
					<param name="allowscriptaccess" value="always"/>
					<param name="flashvars" value="file=<?php echo $lnk."&autostart=".$start."&height=".$params->get('mp_height')."&width=".$params->get('mp_width').$callback; ?>"/>
					<embed src="<?php echo $player; ?>"
						width="<?php echo $params->get('mp_width'); ?>"
						height="<?php echo $params->get('mp_height'); ?>"
						wmode="transparent"
						allowscriptaccess="always"
						allowfullscreen="true"
						flashvars="file=<?php echo $lnk."&autostart=".$start."&height=".$params->get('mp_height')."&width=".$params->get('mp_width').$callback; ?>"
					/>
			</object>
			<?php
			$pp_h = $params->get('mp_height') + 100 + $params->get('popup_height');
			$pp_w = $params->get('mp_width') + 130;
		} //flv

		if(strcasecmp(substr($lnk,-4),".wmv") == 0) {
			echo "<object id=mediaplayer width=400 height=323 classid=clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95 type=application/x-oleobject>
			<param name=filename value=$lnk>
			<param name=autostart value=".$start.">
			<param name=transparentatstart value=true>
			<param name=showcontrols value=1>
			<param name=showdisplay value=0>
			<param name=showstatusbar value=1>
			<param name=autosize value=1>
			<param name=animationatstart value=false>
			<embed name=\"MediaPlayer\" src=$lnk width=".$params->get('mp_width')." height=".$params->get('mp_height')." type=application/x-mplayer2 autostart=".$startwmp." showcontrols=1 showstatusbar=1 transparentatstart=1 animationatstart=0 loop=false pluginspage=http://www.microsoft.com/windows/windowsmedia/download/default.asp></embed>
			</object>";
			$pp_h = $params->get('mp_height') + 100 + $params->get('popup_height');
			$pp_w = $params->get('mp_width') + 130;
		} //wmv

		return ($pp_h."/".$pp_w);
	}

	function insertTime($time) {
		$tmp = explode(":",$time);
		if ($tmp[0] == 0) {
			$return = $tmp[1].":".$tmp[2];
		} else {
			$return = $tmp[0].":".$tmp[1].":".$tmp[2];
		}
		
		return($return);
	} // end of insertTime

	function fu_logoffbtn () {
		$str = "<FORM><INPUT TYPE=\"BUTTON\" VALUE=\"".JText::_('FU_LOGOUT')."\" ONCLICK=\"window.location.href='index.php?option=com_sermonspeaker&task=fu_logout'\"> </FORM>";
		return $str;
	} // end of fu_logoffbtn
}