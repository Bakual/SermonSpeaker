<?php
defined('_JEXEC') or die;

/**
 * Sermonspeaker Component Player Helper
 */
class SermonspeakerHelperPlayer {
	public $mspace;
	public $script;
	public $playlist;
	public $popup;
	public $status;	// Maybe unneeded. Is mostly used to detect an error in the layout
	public $toggle;	// is able to toggle between audio and video
	public $player;	// name of player
	public $file;

	protected $params;
	protected $prio;
	protected $start;
	protected $item;
	protected $count;
	protected $config;
	// Static switches if the script for a player is already loaded
	protected static $jwscript;
	protected static $poscript;
	protected static $fwscript;
	protected static $wmvscript;
	protected static $vimeoscript;

	public function __construct()
	{
		// Get params
		$this->params	= JFactory::getApplication()->getParams('com_sermonspeaker');
	}

	public function getFallbackPlayer($file)
	{
		$ext	= JFile::getExt($item);
		if ($ext == 'wmv' || $ext == 'wma')
		{
			// WMV Player
			return 'wmvplayer';
		}
		elseif ((strpos($this->file, 'http://vimeo.com') === 0) || (strpos($this->file, 'http://player.vimeo.com') === 0))
		{
			// Vimeo
			return 'vimeo';
		}
		else
		{
			// Default: JW Player, plays most files
			return 'jwplayer5';
		}
	}

	private function SinglePlayer(){
		// Choosing the default file to play based on prio and availablity, also check if Fileswitch is possible
		$this->toggle	= false;
		if (($this->config['type'] != 'video') && ($this->item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$this->item->videofile))){
			// We take the audiofile
			$this->file = SermonspeakerHelperSermonspeaker::makeLink($this->item->audiofile);
			if ($this->params->get('fileswitch') && $this->item->videofile){
				$this->playlist['audio']	= $this->file;
				$this->playlist['video']	= SermonspeakerHelperSermonspeaker::makeLink($this->item->videofile);
				$this->toggle	= true;
			}
		} elseif (($this->config['type'] != 'audio') && ($this->item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$this->item->audiofile))){
			// We take the videofile
			$this->file = SermonspeakerHelperSermonspeaker::makeLink($this->item->videofile);
			if ($this->params->get('fileswitch') && $this->item->audiofile){
				$this->playlist['audio']	= SermonspeakerHelperSermonspeaker::makeLink($this->item->audiofile);
				$this->playlist['video']	= $this->file;
				$this->toggle	= true;
			}
		} else {
			// Nothing available
			$this->popup['height']	= 0;
			$this->popup['width']	= 0;
			$this->error	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
			return;
		}

		// Get extension of file
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($this->file);

		if (($ext == 'mp3') && ($this->config['alt_player'] == 1)){
			// PixelOut only if MP3
			$this->PixelOut();
			return;
		}

		// Declare the supported file extensions for Flash
		$audio_ext = array('aac', 'm4a', 'mp3');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2');
		if(in_array($ext, $audio_ext)){
			// Audio File
			$this->setDimensions('23px', '250px');
			$this->setPopup('a');
			$this->status = 'audio';
			if (($this->config['alt_player'] == 2) && ($ext == 'mp3')){
				$this->FlowPlayer();
			} else {
				$this->JWPlayer();
			}
		} elseif(in_array($ext, $video_ext)) {
			// Video File
			$this->setDimensions('23px', '250px');
			$this->setPopup('v');
			$this->status = 'video';
			if ($this->config['alt_player'] == 2){
				$this->FlowPlayer();
			} else {
				$this->JWPlayer();
			}
		} elseif(strpos($this->file, 'http://www.youtube.com') === 0){
			// Youtube File, can only be played by JW Player
			$this->setDimensions('23px', '250px');
			$this->setPopup('v');
			$this->status = 'video';
			$this->JWPlayer();
		} elseif($ext == 'wmv'){
			// WMV File
			$this->setDimensions('21px', '250px');
			$this->setPopup('v');
			$this->status = 'video';
			$this->WMVPlayer();
		} elseif($ext == 'wma'){
			// WMA File
			$this->setDimensions('21px', '250px');
			$this->setPopup('a');
			$this->status = 'audio';
			$this->WMVPlayer();
		} elseif((strpos($this->file, 'http://vimeo.com') === 0) || (strpos($this->file, 'http://player.vimeo.com') === 0)){
			// Vimeo
			$this->setDimensions('23px', '250px');
			$this->Vimeo();
		} else {
			$this->setDimensions('23px', '250px');
			$this->popup['height'] = 0;
			$this->popup['width']  = 0;
			$this->error  = 'Unsupported Filetype';
			$this->toggle = false;
		}
		return;
	}


	/* FlowPayer */
	private function FlowPlayer()
	{
		$this->player = 'FlowPlayer';
		$player = JURI::base(true).'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.11.swf';
		// Load plugins
		$showplaylist = ($this->status == 'playlist') ? 'true' : 'false';
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

		if ($this->status == 'playlist')
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
			foreach ($this->item as $temp_item)
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
			$type	= ($this->status == 'audio') ? 'a' : 'v';
			$cat	= ($this->status == 'audio') ? 'Audio' : 'Video';
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
				if ($this->status != 'playlist')
				{
					$url = 'index.php?&task=download&id='.$this->item->slug.'&type=';
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

	/* PixelOut */
	private function PixelOut()
	{
		$this->player = 'PixelOut';
		$start = $this->config['autostart'] ? 'yes' : 'no';
		if($this->status == 'playlist')
		{
			$this->mspace = '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH').'</div>';
			$this->setDimensions(23, '100%');
			$files		= array();
			$titles		= array();
			$artists	= array();
			foreach($this->item as $item)
			{
				if (($this->config['type'] != 'video') && ($item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$item->videofile))){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->audiofile));
				} elseif (($this->config['type'] != 'audio') && ($item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$item->audiofile))){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->videofile));
				} else {
					$files[]	= urlencode(JURI::root());
					$titles[]	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
					$artists[]	= '';
					continue;
				}
				$titles[]	= ($item->sermon_title) ? urlencode($item->sermon_title) : '';
				$artists[]	= ($item->name) ? urlencode($item->name) : '';
			}
			$file	= implode(',',$files);
			$title	= 'titles: "'.implode(',',$titles).'",';
			$artist	= 'artists: "'.implode(',',$artists).'",';
		}
		else
		{
			$this->mspace = '<div id="mediaspace'.$this->config['count'].'"><audio src="'.$this->file.'" controls="controls" preload="auto" >'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH').'</audio></div>';
			$this->setDimensions(23, 290);
			$file	= urlencode($this->file);
			$title	= ($this->item->sermon_title) ? 'titles: "'.urlencode($this->item->sermon_title).'",' : '';
			$artist	= ($this->item->name) ? 'artists: "'.urlencode($this->item->name).'",' : '';
		}
		$this->script = '<script type="text/javascript">'
							.'AudioPlayer.embed("mediaspace'.$this->config['count'].'", {'
								.'soundFile: "'.$file.'",'
								.$title.$artist
								.'autostart: "'.$start.'"'
							.'})'
						.'</script>';
		$this->toggle = false;
		$this->setPopup();
		$this->status = 'audio';

		// Loading needed Javascript only once
		if (!self::$poscript)
		{
			JHTML::Script('media/com_sermonspeaker/player/audio_player/audio-player.js');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'media/com_sermonspeaker/player/audio_player/player.swf", {
					width: "'.$this->config['awidth'].'",
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			self::$poscript = 1;
		}
		return;
	}

	private function WMVPlayer()
	{
		$this->player = 'WMVPlayer';
		$player	= JURI::root().'media/com_sermonspeaker/player/wmvplayer/wmvplayer.xaml';
		$start	= $this->config['autostart'] ? 1 : 0;
		$type	= ($this->status == 'audio') ? 'a' : 'v';
		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_JAVASCRIPT').'</div>';
		$image = SermonspeakerHelperSermonspeaker::insertPicture($this->item);
		if ($image)
		{
			$image = "'image':'".$image."',";
		}
		$duration = '';
		if ($this->item->sermon_time != '00:00:00')
		{
			$time_arr = explode(':', $this->item->sermon_time);
			$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
			$duration = 'duration: '.$seconds.',';
		}
		$start = $this->config['autostart'] ? 'true' : 'false';
		$this->script	= '<script type="text/javascript">'
							.'var elm = document.getElementById("mediaspace'.$this->config['count'].'");'
							.'var cfg = {'
							."	file:'".$this->file."',"
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

	private function MediaPlayer()
	{
		$this->player = 'MediaPlayer';
		$this->setDimensions(50, '100%');
		$start = $this->config['autostart'] ? 1 : 0;
		$this->mspace = '<object id="mediaplayer" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
							.'<param name="filename" value="'.$this->file.'">'
							.'<param name="autostart" value="'.$start.'">'
							.'<param name="transparentatstart" value="true">'
							.'<param name="showcontrols" value="1">'
							.'<param name="showdisplay" value="0">'
							.'<param name="showstatusbar" value="1">'
							.'<param name="autosize" value="1">'
							.'<param name="animationatstart" value="false">'
							.'<embed name="MediaPlayer" src="'.$this->file.'" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" type="application/x-mplayer2" autostart="'.$start.'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
							.'</embed>'
						.'</object>';
		$this->script = '';
		$this->setPopup('v');
		$this->status = 'video';
		$this->toggle = false;
		return;
	}

	private function Vimeo()
	{
		$this->player	= 'Vimeo';
		$id				= trim(strrchr($this->file, '/'), '/ ');
		$this->file		= 'http://vimeo.com/'.$id;
		$this->fb_file	= 'http://vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
		$this->setDimensions(50, '100%');
		$start = $this->config['autostart'] ? 1 : 0;
		$this->mspace = '<iframe id="mediaspace'.$this->config['count'].'" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" '
						.'src="http://player.vimeo.com/video/'.$id.'?title=0&byline=0&portrait=0&border=0&autoplay='.$start.'&player_id=vimeo'.$this->config['count'].'&api=1">'
						.'</iframe>';
		$this->script	= '';
		$this->setPopup('v');
		$this->status	= 'video';
		$this->toggle	= false;

		// Loading needed Javascript only once
		if (!self::$vimeoscript)
		{
			if ($this->params->get('ga_id', ''))
			{
				JHTML::Script('media/com_sermonspeaker/player/vimeo/ganalytics.js', true);
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration("window.addEvent('domready', _trackVimeo);");
			}
			self::$vimeoscript = 1;
		}
		return;
	}

	// Sets the dimensions of the player for audio and video. $height and $width are default values.
	private function setDimensions($height, $width)
	{
		$this->config['aheight']	= (isset($this->config['aheight'])) ? $this->config['aheight'] : $this->params->get('aheight', $height);
		$this->config['awidth']		= (isset($this->config['awidth'])) ? $this->config['awidth'] : $this->params->get('awidth', $width);
		$this->config['vheight']	= (isset($this->config['vheight'])) ? $this->config['vheight'] : $this->params->get('vheight', '300px');
		$this->config['vwidth']		= (isset($this->config['vwidth'])) ? $this->config['vwidth'] : $this->params->get('vwidth', '100%');
		return;
	}

	// Sets the dimensions of the Popup window. $type can be 'a' (audio) or 'v' (video)
	private function setPopup($type = 'a')
	{
		$this->popup['width']	= (strpos($this->config[$type.'width'], '%')) ? 500 : $this->config[$type.'width'] + 130;
		$this->popup['height']	= $this->config[$type.'height'] + $this->params->get('popup_height');
	}
}