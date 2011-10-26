<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Player Helper
 */
class SermonspeakerHelperPlayer {
	public $mspace;
	public $script;
	public $playlist;
	public $popup;
	public $status;
	public $toggle;
	public $player;
	public $file;

	private $params;
	private $prio;
	private $start;
	private $item;
	private $count;
	private $config;
	// Static switches if the script for a player is already loaded
	private static $jwscript;
	private static $poscript;

	/**
	 * Constructor 
	 * Takes two arguments:
	 * $item can be a single sermon object or an array of sermon objects
	 * $config should be an array of config options. Valid options:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 */
	public function __construct($item, $config = array()) {
		// Get params
		$app = JFactory::getApplication();
		$this->params	= $app->getParams();

		// defining some variables
		$this->item		= $item;

		if (!is_array($config)){
			JError::raiseWarning(100, 'Wrong calling of player helper, second parameter needs to be an array');
			$config = array();
		}

		if(!isset($config['count'])){
			$config['count'] = 1;
		}

		// Allow a fixed value for the type; may be audio, video or auto. "Auto" is default behaviour and takes care of the "prio" param.
		if (!isset($config['type'])){
			$config['type'] = 'auto';
		}
		$this->prio		= $this->params->get('fileprio', 0);

		// Autostart parameter may be overridden by a layout (eg for Series/Sermon View)
		if (!isset($config['autostart'])){
			$config['autostart']	= $this->params->get('autostart');
		}

		// Allow a player to be chosen by the layout (eg for icon layout); 0 = JWPlayer, 1 = PixelOut, 2 = FlowPlayer
		if (!isset($config['alt_player'])){
			$config['alt_player']	= $this->params->get('alt_player');
		}

		$this->config = $config;

		// Dispatching
		if(is_array($this->item)){
			// Playlist
			$this->status	= 'playlist';
			switch ($config['alt_player']){
				case 1:
					$this->PixelOut();
					break;
				case 2:
					$this->FlowPlayer();
					break;
				default:
					$this->JWPlayer();
					break;
			}
			return;
		} else {
			// Single File
			$this->SinglePlayer();
			return;
		}
	}

	private function SinglePlayer(){
		// Choosing the default file to play based on prio and availablity, also check if Fileswitch is possible
		$this->toggle	= false;
		if (($this->config['type'] != 'video') && ($this->item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$this->item->videofile))){
			// We take the audiofile
			$this->file = SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile);
			if ($this->params->get('fileswitch') && $this->item->videofile){
				$this->playlist['audio']	= '{file: "'.$this->file.'"}';
				$this->playlist['video']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->videofile).'"}';
				$this->toggle	= true;
			}
		} elseif (($this->config['type'] != 'audio') && ($this->item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$this->item->audiofile))){
			// We take the videofile
			$this->file = SermonspeakerHelperSermonspeaker::makelink($this->item->videofile);
			if ($this->params->get('fileswitch') && $this->item->audiofile){
				$this->playlist['audio']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile).'"}';
				$this->playlist['video']	= '{file: "'.$this->file.'"}';
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
		$this->setDimensions('23px', '250px');
		if(in_array($ext, $audio_ext)){
			// Audio File
			$this->setPopup('a');
			$this->status = 'audio';
			if (($this->config['alt_player'] == 2) && ($ext == 'mp3')){
				$this->FlowPlayer();
			} else {
				$this->JWPlayer();
			}
		} elseif(in_array($ext, $video_ext)) {
			// Video File
			$this->setPopup('v');
			$this->status = 'video';
			if ($this->config['alt_player'] == 2){
				$this->FlowPlayer();
			} else {
				$this->JWPlayer();
			}
		} elseif(strpos($this->file, 'http://www.youtube.com') === 0){
			// Youtube File, can only be played by JW Player
			$this->setPopup('v');
			$this->status = 'video';
			$this->JWPlayer();
		} elseif($ext == 'wmv'){
			// WMV File
			// TODO: Switch to Longtail Silverlight player for wmv and wma support
			$this->MediaPlayer();
		} elseif(strpos($this->file, 'http://vimeo.com') === 0 || (strpos($this->file, 'http://player.vimeo.com') === 0)){
			// Vimeo
			$this->Vimeo();
		} else {
			$this->popup['height'] = 0;
			$this->popup['width']  = 0;
			$this->error  = 'Unsupported Filetype';
			$this->toggle = false;
		}
		return;
	}

	private function JWPlayer(){
		$this->player = 'JWPlayer';
		$this->mspace = '<div id="mediaspace'.$this->config['count'].'">Flashplayer needs Javascript turned on</div>';
		$player = JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
		// Setting some general player options
		$start = $this->config['autostart'] ? 'true' : 'false';
		$options	= '"autostart": '.$start.',';
		$skin	= $this->params->get('jwskin', '');
		if ($skin){
			$options	.= 'skin: "'.$skin.'",';
		}
		if($this->status == 'playlist'){
			$this->toggle = $this->params->get('fileswitch', 0);
			$this->setDimensions('23px', '100%');
			$type	= ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->prio)) ? 'a' : 'v';
			$this->setPopup($type);
			$options	.= 'events: {'
								.'onPlaylistItem: function(event){'
									.'var i = 0;'
									.'while (document.id("sermon"+i)){'
										.'document.id("sermon"+i).removeClass("ss-current");'
											.'i++;'
										.'}'
									.'document.id("sermon"+event.index).addClass("ss-current");'
									.'item = jwplayer().getPlaylistItem();'
									.'time = new Array();'
									.'var hrs = Math.floor(item.duration/3600);'
									.'if (hrs > 0){time.push(hrs);}'
									.'var min = Math.floor((item.duration - hrs * 3600)/60);'
									.'if (min > 0){time.push(min);}'
									.'time.push(item.duration - hrs * 3600 - min * 60);'
									.'var duration = time.join(":");'
									."document.id('playing').innerHTML = '<img src=\"'+item.image+'\" class=\"picture\" /><span class=\"duration\">'+duration+'</span><div class=\"text\"><span class=\"title\">'+item.title+'</span><span class=\"desc\">'+item.description+'</span></div>';"
								.'}'
							.'},';
			$entries = array();
			foreach ($this->item as $temp_item){
				// Choosing the default file to play based on prio and availabilty
				if (($this->config['type'] != 'video') && ($temp_item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$temp_item->videofile))){
					$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile).'"';
					$title = ', title: "'.$temp_item->sermon_title.'"';
				} elseif (($this->config['type'] != 'audio') && ($temp_item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$temp_item->audiofile))){
					$file = 'file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile).'"';
					$title = ', title: "'.$temp_item->sermon_title.'"';
				} else {
					$file = 'file: "'.JURI::root().'"';
					$title = ', title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
				}
				$desc = '';
				if ($temp_item->sermon_date){
					$desc[] = JText::_('JDATE').': '.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), 'UTC');
				}
				if ($temp_item->name){
					$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER').': '.$temp_item->name;
				}
				$meta = '';
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
				if ($this->toggle){
					// Preparing specific playlists for audio and video
					if ($temp_item->audiofile){
						$audios[] = '{file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile).'"'.$title.$meta.'}';
					} else {
						$audios[] = '{file: "'.JURI::root().'", title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"'.$meta.'}';
					}
					$this->playlist['audio'] = implode(',',$audios);
					if ($temp_item->videofile){
						$videos[] = '{file: "'.SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile).'"'.$title.$meta.'}';
					} else {
						$videos[] = '{file: "'.JURI::root().'", title: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"'.$meta.'}';
					}
					$this->playlist['video'] = implode(',', $videos);
				}
			}
		} else {
			$type	= ($this->status == 'audio') ? 'a' : 'v';
			$image = '';
			if ($this->item->picture){
				$image = 'image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->picture).'",';
			} elseif ($this->item->pic){
				$image = 'image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->pic).'",';
			}
			$duration = '';
			if ($this->item->sermon_time != '00:00:00'){
				$time_arr = explode(':', $this->item->sermon_time);
				$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
				$duration = 'duration: '.$seconds.',';
			}
			$entries[] = '{file:"'.$this->file.'",'.$image.$duration.'}';
		}
		$this->playlist['default'] = implode(',', $entries);
		$this->script	= '<script type="text/javascript">'
							.'jwplayer("mediaspace'.$this->config['count'].'").setup({'
								.'flashplayer: "'.$player.'",'
								.'playlist: ['.$this->playlist['default'].'],'
								.'controlbar: "bottom",'
								.$options
								.'width: "'.$this->config[$type.'width'].'",'
								.'height: "'.$this->config[$type.'height'].'"'
							.'});'
						.'</script>';

		// Loading needed Javascript only once
		if (!self::$jwscript){
			JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js');
			if ($this->toggle){
				$awidth		= is_numeric($this->config['awidth']) ? $this->config['awidth'].'px' : $this->config['awidth'];
				$aheight	= is_numeric($this->config['aheight']) ? $this->config['aheight'].'px' : $this->config['aheight'];
				$vwidth		= is_numeric($this->config['vwidth']) ? $this->config['vwidth'].'px' : $this->config['vwidth'];
				$vheight	= is_numeric($this->config['vheight']) ? $this->config['vheight'].'px' : $this->config['vheight'];
				if ($this->status != 'playlist'){
					$url = 'index.php?&task=download&id='.$this->item->slug.'&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'video').'\'};';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'audio').'\'};';
				} else {
					$download_video = '';
					$download_audio = '';
				}
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration('
					function Video() {
						jwplayer().load(['.$this->playlist['video'].']).resize("'.$vwidth.'","'.$vheight.'");
						document.getElementById("mediaspace'.$this->config['count'].'_wrapper").style.width="'.$vwidth.'";
						document.getElementById("mediaspace'.$this->config['count'].'_wrapper").style.height="'.$vheight.'";
						'.$download_video.'
					}
				');
				$doc->addScriptDeclaration('
					function Audio() {
						jwplayer().load(['.$this->playlist['audio'].']).resize("'.$awidth.'","'.$aheight.'");
						document.getElementById("mediaspace'.$this->config['count'].'_wrapper").style.width="'.$awidth.'";
						document.getElementById("mediaspace'.$this->config['count'].'_wrapper").style.height="'.$aheight.'";
						'.$download_audio.'
					}
				');
			}
			self::$jwscript = 1;
		}
		return;
	}

	/* FlowPayer */
	private function FlowPlayer(){
		$this->player = 'FlowPlayer';
		$player	= JURI::root().'media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.7.swf';
		$start	= $this->config['autostart'] ? 'true' : 'false';

		if($this->status == 'playlist'){
			$option	= 'playlist:true,';
			$this->toggle = $this->params->get('fileswitch', 0);
			$this->setDimensions('23px', '100%');
			$type	= ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->prio)) ? 'a' : 'v';
			foreach ($this->item as $temp_item){
				// Choosing the default file to play based on prio and availabilty
				if (($this->config['type'] != 'video') && ($temp_item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$temp_item->videofile))){
					$files[] = SermonspeakerHelperSermonspeaker::makelink($temp_item->audiofile);
				} elseif (($this->config['type'] != 'audio') && ($temp_item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$temp_item->audiofile))){
					$files[] = SermonspeakerHelperSermonspeaker::makelink($temp_item->videofile);
				} else {
					$files[] = JURI::root();
				}
			}
			$playlist	= '"'.implode('","', $files).'"';
		} else {
			$option = '';
			$type	= ($this->status == 'audio') ? 'a' : 'v';
			$playlist	= '"'.$this->file.'"';
		}
		$this->mspace = '<div style="display:block; width:'.$this->config[$type.'width'].'; height:'.$this->config[$type.'height'].'" id="mediaspace'.$this->config['count'].'"></div>';
		$this->script = '<script type="text/javascript">'
							.'	flowplayer("mediaspace'.$this->config['count'].'", "'.$player.'", {'
							.'	  plugins: {'
							.'		controls: {'
							.$option
							.'		  fullscreen: false,'
							.'		  height: 23,'
							.'		  autoHide: false'
							.'		}'
							.'	  },'
							.'	clip: {'
							.'	  autoPlay: '.$start
							.'	  },'
							.'	playlist: ['
							.'	  '.$playlist
							.'	  ]'
							.'	});'
							.'</script>';

		// Loading needed Javascript only once
		if (!self::$jwscript){
			JHTML::Script('media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.6.min.js');
			self::$jwscript = 1;
		}
		return;
	}

	private function PixelOut(){
		$this->player = 'PixelOut';
		$this->mspace = '<div id="mediaspace'.$this->config['count'].'">Flashplayer needs Javascript turned on</div>';
		$start = $this->config['autostart'] ? 'yes' : 'no';
		if($this->status == 'playlist'){
			$this->setDimensions(23, '100%');
			$files		= array();
			$titles		= array();
			$artists	= array();
			foreach($this->item as $item){
				if (($this->config['type'] != 'video') && ($item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$item->videofile))){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makelink($item->audiofile));
				} elseif (($this->config['type'] != 'audio') && ($item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$item->audiofile))){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makelink($item->videofile));
				} else {
					$files[]	= urlencode(JURI::root());
					$titles[]	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
					$artists	= '';
					continue;
				}
				$titles[]	= ($item->sermon_title) ? $item->sermon_title : '';
				$artists[]	= ($item->name) ? $item->name : '';
			}
			$file	= implode(',',$files);
			$title	= 'titles: "'.implode(',',$titles).'",';
			$artist	= 'artists: "'.implode(',',$artists).'",';
		} else {
			$this->setDimensions(23, 290);
			$file	= urlencode($this->file);
			$title	= ($this->item->sermon_title) ? 'titles: "'.$this->item->sermon_title.'",' : '';
			$artist	= ($this->item->name) ? 'artists: "'.$this->item->name.'",' : '';
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
		if (!self::$poscript){
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

	private function MediaPlayer(){
		$this->player = 'MediaPlayer';
		$this->setDimensions(50, '100%');
		$start = $this->config['autostart'] ? 1 : 0;
		$this->mspace = '<object id="mediaplayer" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
							.'	<param name="filename" value="'.$this->file.'">'
							.'	<param name="autostart" value="'.$start.'">'
							.'	<param name="transparentatstart" value="true">'
							.'	<param name="showcontrols" value="1">'
							.'	<param name="showdisplay" value="0">'
							.'	<param name="showstatusbar" value="1">'
							.'	<param name="autosize" value="1">'
							.'	<param name="animationatstart" value="false">'
							.'	<embed name="MediaPlayer" src="'.$this->file.'" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" type="application/x-mplayer2" autostart="'.$start.'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
							.'	</embed>'
							.'</object>';
		$this->script = '';
		$this->setPopup('v');
		$this->status = 'video';
		$this->toggle = false;
		return;
	}

	private function Vimeo(){
		$this->player	= 'Vimeo';
		$id				= trim(strrchr($this->file, '/'), '/ ');
		$this->file		= 'http://vimeo.com/'.$id;
		$this->fb_file	= 'http://vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
		$this->setDimensions(50, '100%');
		$start = $this->config['autostart'] ? 1 : 0;
		$this->mspace = '<iframe id="mediaspace'.$this->config['count'].'" width="'.$this->config['vwidth'].'" height="'.$this->config['vheight'].'" '
						.'src="http://player.vimeo.com/video/'.$id.'?title=0&byline=0&portrait=0&border=0&autoplay='.$start.'&player_id="vimeo'.$this->config['count'].'">'
						.'</iframe>';
		$this->script	= '';
		$this->setPopup('v');
		$this->status	= 'video';
		$this->toggle	= false;
		return;
	}

	// Sets the dimensions of the player for audio and video. $height and $width are default values.
	private function setDimensions($height, $width){
		if (!isset($this->config['aheight'])){
			$this->config['aheight']	= $height;
		}
		if (!isset($this->config['awidth'])){
			$this->config['awidth']	= $width;
		}
		if (!isset($this->config['vheight'])){
			$this->config['vheight']	= $this->params->get('mp_height');
		}
		if (!isset($this->config['vwidth'])){
			$this->config['vwidth']		= $this->params->get('mp_width');
		}
		return;
	}

	// Sets the dimensions of the Popup window. $type can be 'a' (audio) or 'v' (video)
	private function setPopup($type = 'a'){
		if (strpos($this->config[$type.'width'], '%')){
			$this->popup['width'] = 500;
		} else {
			$this->popup['width'] = $this->config[$type.'width'] + 130;
		}
		$this->popup['height'] = $this->config[$type.'height'] + $this->params->get('popup_height');
	}
}