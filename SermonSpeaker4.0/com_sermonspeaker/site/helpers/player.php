<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component Player Helper
 */
class SermonspeakerHelperPlayer {
	public $mspace;
	public $script;
	public $playlist;
	public $width;
	public $height;
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

	public function __construct($params) {
		$this->params	= $params;
		$this->prio		= $this->params->get('fileprio', 0);

		// Rem: may be improved to a simple BOOL variable and then checked in the respective playerfunction
		if ($this->params->get('autostart') && (JRequest::getCmd('view') != 'seriessermon')){
			$this->start[0]='true'; $this->start[1]='1'; $this->start[2]='yes';
		} else {
			$this->start[0]='false'; $this->start[1]='0'; $this->start[2]='no';
		}
	}

	public function prepare($item, $count = '1') {
		// defining some variables
		$this->item		= $item;
		$this->count	= $count;

		// Dispatching
		if(is_array($this->item)){
			// Playlist
			return $this->PlaylistPlayer();
		} else {
			// Single File
			return $this->SinglePlayer();
		}
	}

	private function PlaylistPlayer(){
		if($this->params->get('alt_player')){
			$this->PixelOut(1);
			return;
		}
		// JWPlayer
		$this->toggle = ($this->params->get('fileswitch', 0)) ? true : false;
		$this->player = 'JWPlayer';
		$player	= JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
		if (!$this->height){
			if($this->prio){
				$this->height	= $this->params->get('mp_height');
			} else {
				$this->height	= '80px';
			}
		}
		if (!$this->width){
			$this->width	= '100%';
		}

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
				$this->playlist['video'] = implode(',',$videos);
			}
		}
		$this->playlist['default'] = implode(',', $entries);
		$this->mspace	= '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
		$this->script	= '<script type="text/javascript">'
							.'	jwplayer("mediaspace'.$this->count.'").setup({'
							.'	  flashplayer: "'.$player.'",'
							.'	  playlist: ['
							.$this->playlist['default']
							.'	  ],'
							.'	  "playlist.size": 60,'
							.'	  "playlist.position": "top",'
							.'	  autostart: '.$this->start[0].','
							.'	  controlbar: "bottom",'
							.'	  width: "'.$this->width.'",'
							.'	  height: "'.$this->height.'",'
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
		$this->status	= 'playlist';
		
		return;
	}

	private function SinglePlayer(){
		// Choosing the default file to play based on prio and availablity, also check if Fileswitch is possible
		$this->toggle	= false;
		if (($this->item->audiofile && !$this->prio) || ($this->item->audiofile && !$this->item->videofile)){
			$this->file = SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile);
			if ($this->params->get('fileswitch') && $this->item->videofile){
				$this->playlist['audio']	= '{file: "'.$this->file.'"}';
				$this->playlist['video']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->videofile).'"}';
				$this->toggle	= true;
			}
		} elseif (($this->item->videofile && $this->prio) || ($this->item->videofile && !$this->item->audiofile)){
			$this->file = SermonspeakerHelperSermonspeaker::makelink($this->item->videofile);
			if ($this->params->get('fileswitch') && $this->item->audiofile){
				$this->playlist['audio']	= '{file: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->audiofile).'"}';
				$this->playlist['video']	= '{file: "'.$this->file.'"}';
				$this->toggle	= true;
			}
		} else {
			$this->file   = '';
			$this->player = '';
			$this->mspace = '';
			$this->script = '';
			$this->popup['height'] = 0;
			$this->popup['width']  = 0;
			$this->status = false;
			$this->error  = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
			return;
		}
		// Get extension of file
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($this->file);

		if (($ext == 'mp3') && $this->params->get('alt_player')){
			// PixelOut
			$this->PixelOut();
			return;
		}

		// Declare the supported file extensions for JW Player
		$audio_ext = array('aac', 'm4a', 'mp3');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2');
		if(in_array($ext, $audio_ext)){
			// Audio File
			$this->height	= ($this->height) ? $this->height : '23px';
			$this->width	= ($this->width) ? $this->width : '250px';
			$this->popup['height']	= $this->params->get('popup_height') + 23;
			$this->popup['width']	= '380';
			$this->status = 'audio';
			$this->JWPlayer();
		} elseif(in_array($ext, $video_ext) || (strpos($this->file, 'http://www.youtube.com') === 0)) {
			// Video File
			$this->height	= ($this->height) ? $this->height : $this->params->get('mp_height');
			$this->width	= ($this->width) ? $this->width : $this->params->get('mp_width');
			$this->popup['height']	= $this->params->get('popup_height') + $this->height;
			if (strpos($this->width, '%')){
				$this->popup['width'] = 500;
			} else {
				$this->popup['width'] = $this->width + 130;
			}
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
			$this->player = '';
			$this->mspace = '';
			$this->script = '';
			$this->popup['height'] = 0;
			$this->popup['width']  = 0;
			$this->status = false;
			$this->error  = 'Unsupported Filetype';
			$this->toggle = false;
		}
		return;
	}

	private function JWPlayer($multi=0){
		$this->player = 'JWPlayer';
		$this->mspace = '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
		$player = JURI::root().'media/com_sermonspeaker/player/jwplayer/player.swf';
		if($multi){
		} else {
			$image = '';
			if ($this->item->picture){
				$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->picture).'",';
			} elseif ($this->item->pic){
				$image .= '	  image: "'.SermonspeakerHelperSermonspeaker::makelink($this->item->pic).'",';
			}
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
		$this->script = '<script type="text/javascript">'
							.'	jwplayer("mediaspace'.$this->count.'").setup({'
							.'	  flashplayer: "'.$player.'",'
							.'	  file: "'.$this->file.'",'
							.'	  autostart: '.$this->start[0].','
							.$duration
							.$skin
							.$image
							.'	  controlbar: "bottom",'
							.'	  width: "'.$this->width.'",'
							.'	  height: "'.$this->height.'"'
							.'	});'
							.'</script>';
		return;
	}

	private function PixelOut($multi=0){
		$this->player = 'PixelOut';
		$this->mspace = '<div id="mediaspace'.$this->count.'">Flashplayer needs Javascript turned on</div>';
		if($multi){
			$files		= array();
			$titles		= array();
			$artists	= array();
			foreach($this->item as $item){
				if (($item->audiofile && !$this->prio) || ($item->audiofile && !$item->videofile)){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makelink($item->audiofile));
				} elseif (($item->videofile && $this->prio) || ($item->videofile && !$item->audiofile)){
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
			$file	= urlencode($this->file);
			$title	= ($this->item->sermon_title) ? 'titles: "'.$this->item->sermon_title.'",' : '';
			$artist	= ($this->item->name) ? 'artists: "'.$this->item->name.'",' : '';
		}
		$this->script = '<script type="text/javascript">'
							.'AudioPlayer.embed("mediaspace'.$this->count.'", {'
								.'soundFile: "'.$file.'",'
								.$title.$artist
								.'autostart: "'.$this->start[2].'"'
							.'})'
						.'</script>';
		$this->toggle = false;
		$this->popup['height'] = $this->params->get('popup_height') + 23;
		$this->popup['width']  = '380';
		$this->status = 'audio';
		return;
	}

	private function MediaPlayer(){
		$this->player = 'MediaPlayer';
		$this->height	= ($this->height) ? $this->height : $this->params->get('mp_height');
		$this->width	= ($this->width) ? $this->width : $this->params->get('mp_width');
		$this->mspace = '<object id="mediaplayer" width="'.$this->width.'" height="'.$this->height.'" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95" type="application/x-oleobject">'
							.'	<param name="filename" value="'.$this->file.'">'
							.'	<param name="autostart" value="'.$this->start[1].'">'
							.'	<param name="transparentatstart" value="true">'
							.'	<param name="showcontrols" value="1">'
							.'	<param name="showdisplay" value="0">'
							.'	<param name="showstatusbar" value="1">'
							.'	<param name="autosize" value="1">'
							.'	<param name="animationatstart" value="false">'
							.'	<embed name="MediaPlayer" src="'.$this->file.'" width="'.$this->width.'" height="'.$this->height.'" type="application/x-mplayer2" autostart="'.$this->start[1].'" showcontrols="1" showstatusbar="1" transparentatstart="1" animationatstart="0" loop="false" pluginspage="http://www.microsoft.com/windows/windowsmedia/download/default.asp">'
							.'	</embed>'
							.'</object>';
		$this->script = '';
		$this->popup['height'] = $this->height + $this->params->get('popup_height');
		$this->popup['width']  = $this->width + 130;
		$this->status = 'video';
		$this->toggle = false;
		return;
	}

	private function Vimeo(){
		$this->player	= 'Vimeo';
		$id				= trim(strrchr($this->file, '/'), '/ ');
		$this->file		= 'http://vimeo.com/'.$id;
		$this->height	= ($this->height) ? $this->height : $this->params->get('mp_height');
		$this->width	= ($this->width) ? $this->width : $this->params->get('mp_width');
		$this->mspace = '<iframe id="mediaspace'.$this->count.'" width="'.$this->width.'" height="'.$this->height.'" '
						.'src="http://player.vimeo.com/video/'.$id.'?title=0&byline=0&portrait=0&autoplay='.$this->start[1].'&player_id="vimeo'.$this->count.'">'
						.'</iframe>';
		$this->script	= '';
		$this->popup['height'] = $this->height + $this->params->get('popup_height');
		$this->popup['width']  = $this->width + 130;
		$this->status	= 'video';
		$this->toggle	= false;
		return;
	}
}