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
	private static $fwscript;
	private static $wmvscript;

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
		$this->params	= $app->getParams('com_sermonspeaker');

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
			if(!count($this->item)){
				// Nothing available
				$this->popup['height']	= 0;
				$this->popup['width']	= 0;
				$this->error	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
				return;
			}
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

	/* JW Player */
	private function JWPlayer()
	{
		$this->player	= 'JWPlayer';
		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">Flashplayer needs Javascript turned on</div>';
		// Setting some general player options
		$options['flashplayer']	= "'".JURI::base(true)."/media/com_sermonspeaker/player/jwplayer/player.swf'";
		$options['autostart']	= $this->config['autostart'] ? "'true'" : "'false'";
		$options['controlbar']	= "'bottom'";
		if ($skin = $this->params->get('jwskin', ''))
		{
			$options['skin'] = "'".SermonspeakerHelperSermonspeaker::makeLink($skin)."'";
		}
		// Plugins
		if ($ga = $this->params->get('ga_id', ''))
		{
			$plugins['gapro-2'] = '{}';
		}
		$plugins['fbit-1'] = '{}';
		$plugins['tweetit-1'] = '{}';
		$plugins['plusone-1'] = '{}';
//		$plugins['sharing-3'] = '{}';
//		$plugins['viral-2'] = '{}';
//		$plugins['flow-2'] = '{}';
		if (isset($plugins))
		{
			foreach ($plugins as $key => $value)
			{
				$plugin[] = "'".$key."':".$value;
			}
			$plugins = implode(',', $plugin);
			$options['plugins'] = '{'.$plugins.'}';
		}
		if ($this->status == 'playlist')
		{
			$this->toggle = $this->params->get('fileswitch', 0);
			$this->setDimensions('23px', '100%');
			$type = ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->prio)) ? 'a' : 'v';
			$this->setPopup($type);
			$options['events']	= '{'
									.'onPlaylistItem: function(event){'
										.'var i = 0;'
										.'while (document.id("sermon"+i)){'
											.'document.id("sermon"+i).removeClass("ss-current");'
												.'i++;'
											.'}'
										.'document.id("sermon"+event.index).addClass("ss-current");'
										.'entry = jwplayer().getPlaylistItem();'
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
										.'document.id("playing-pic").src = entry.image;'
										.'if(entry.image){'
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
									.'}'
								.'}';
			$entries = array();
			foreach ($this->item as $temp_item)
			{
				$entry = array();
				// Choose picture to show
				$img = SermonspeakerHelperSermonspeaker::insertPicture($temp_item);
				// Choosing the default file to play based on prio and availabilty
				if (($this->config['type'] != 'video') && ($temp_item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$temp_item->videofile)))
				{
					$entry['file']	= "'file':'".SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile)."'";
				}
				elseif (($this->config['type'] != 'audio') && ($temp_item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$temp_item->audiofile)))
				{
					$entry['file']	= "'file':'".SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile)."'";
				}
				else
				{
					if ($img)
					{
						$entry['file']	= "'file':'".$img."'";
					}
					else
					{
						$entry['file']	= "'file':'media/com_sermonspeaker/images/nopict.jpg'";
					}
					$entry['error']	= "'error':'".JText::_('JGLOBAL_RESOURCE_NOT_FOUND')."'";
				}
				$entry[]	= "'title':'".addslashes($temp_item->sermon_title)."'";
				$desc = array();
				if ($temp_item->sermon_date)
				{
					$desc[] = JText::_('JDATE').': '.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), true);
				}
				if ($temp_item->name)
				{
					$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER').': '.addslashes($temp_item->name);
				}
				$entry[] = "'description':'".implode('<br/>', $desc)."'";
				if ($temp_item->sermon_time != '00:00:00')
				{
					$time_arr = explode(':', $temp_item->sermon_time);
					$seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$entry[] = "'duration':'".$seconds."'";
				}
				if ($img)
				{
					$entry[] = "'image':'".$img."'";
				}
				$entries[] = '{'.implode(',', $entry).'}';
				if ($this->toggle)
				{
					// Preparing specific playlists for audio and video
					if ($temp_item->audiofile)
					{
						$entry['file']	= "'file':'".SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile)."'";
						unset($entry['error']);
					}
					else
					{
						if ($img)
						{
							$entry['file']	= "'file':'".$img."'";
						}
						else
						{
							$entry['file']	= "'file':'media/com_sermonspeaker/images/nopict.jpg'";
						}
						$entry['error']	= "'error':'".JText::_('JGLOBAL_RESOURCE_NOT_FOUND')."'";
					}
					$audios[] = '{'.implode(',', $entry).'}';
					if ($temp_item->videofile)
					{
						$entry['file']	= "'file':'".SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile)."'";
						unset($entry['error']);
					}
					else
					{
						if ($img)
						{
							$entry['file']	= "'file':'".$img."'";
						}
						else
						{
							$entry['file']	= "'file':'media/com_sermonspeaker/images/nopict.jpg'";
						}
						$entry['error']	= "'error':'".JText::_('JGLOBAL_RESOURCE_NOT_FOUND')."'";
					}
					$videos[] = '{'.implode(',', $entry).'}';
				}
			}
			$this->playlist['default'] = implode(',', $entries);
			if ($this->toggle)
			{
				$this->playlist['audio'] = implode(',', $audios);
				$this->playlist['video'] = implode(',', $videos);
			}
		}
		else
		{
			$type	= ($this->status == 'audio') ? 'a' : 'v';
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
				$duration = "'duration':'".$seconds."',";
			}
			$this->playlist['default'] = "{'file':'".$this->file."',".$image.$duration."}";
			if ($this->toggle)
			{
				$this->playlist['audio']	= "{'file':'".$this->playlist['audio']."'}";
				$this->playlist['video']	= "{'file':'".$this->playlist['video']."'}";
			}
		}
		foreach ($options as $key => $value)
		{
			$option[] = "'".$key."':".$value;
		}
		$options = implode(',', $option);
		$this->script	= '<script type="text/javascript">'
							."jwplayer('mediaspace".$this->config['count']."').setup({"
								."'playlist':[".$this->playlist['default']."],"
								."'width':'".$this->config[$type.'width']."',"
								."'height':'".$this->config[$type.'height']."',"
								.$options
							.'});'
						.'</script>';

		// Loading needed Javascript only once
		if (!self::$jwscript){
			JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('function ss_play(id){jwplayer().playlistItem(id);}');
			if ($this->toggle){
				$awidth		= is_numeric($this->config['awidth']) ? $this->config['awidth'].'px' : $this->config['awidth'];
				$aheight	= is_numeric($this->config['aheight']) ? $this->config['aheight'].'px' : $this->config['aheight'];
				$vwidth		= is_numeric($this->config['vwidth']) ? $this->config['vwidth'].'px' : $this->config['vwidth'];
				$vheight	= is_numeric($this->config['vheight']) ? $this->config['vheight'].'px' : $this->config['vheight'];
				if ($this->status != 'playlist'){
					$url = 'index.php?&task=download&id='.$this->item->slug.'&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'video').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO').'"';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'audio').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO').'"';
				} else {
					$download_video = '';
					$download_audio = '';
				}
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
		$player	= JURI::root().'media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.11.swf';
		$start	= $this->config['autostart'] ? 'true' : 'false';

		if($this->status == 'playlist'){
			$option	= 'playlist:true,';
			$js		= 'onStart: function(){'
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
					.'},';
			$this->toggle = $this->params->get('fileswitch', 0);
			$this->setDimensions('23px', '100%');
			$type	= ($this->config['type'] == 'audio' || ($this->config['type'] == 'auto' && !$this->prio)) ? 'a' : 'v';
			foreach ($this->item as $temp_item){
				$entry = array();
				// Choose picture to show
				$img = SermonspeakerHelperSermonspeaker::insertPicture($temp_item);
				// Choosing the default file to play based on prio and availabilty
				if (($this->config['type'] != 'video') && ($temp_item->audiofile && (!$this->prio || ($this->config['type'] == 'audio') || !$temp_item->videofile))){
					$entry['file']	= 'url:"'.SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile).'"';
				} elseif (($this->config['type'] != 'audio') && ($temp_item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$temp_item->audiofile))){
					$entry['file']	= 'url:"'.SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile).'"';
				} else {
					if ($img){
						$entry['file']	= 'url: "'.$img.'"';
					} else {
						$entry['file']	= 'url:"media/com_sermonspeaker/images/nopict.jpg"';
					}
					$entry['error']	= 'error: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
				}
				$entry[]	= 'title: "'.addslashes($temp_item->sermon_title).'"';
				if ($temp_item->sermon_time != '00:00:00'){
					$time_arr	= explode(':', $temp_item->sermon_time);
					$seconds	= ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];
					$entry[]	= 'duration: "'.$seconds.'"';
				}
				if ($img){
					$entry[]	= 'coverImage: "'.$img.'"';
				}
				$desc = array();
				if ($temp_item->sermon_date){
					$desc[] = JText::_('JDATE').': '.JHTML::Date($temp_item->sermon_date, JText::_($this->params->get('date_format')), true);
				}
				if ($temp_item->name){
					$desc[] = JText::_('COM_SERMONSPEAKER_SPEAKER').': '.addslashes($temp_item->name);
				}
				$entry[]	= 'description: "'.implode('<br/>', $desc).'"';
				$entries[] = implode(',', $entry);

				if ($this->toggle){
					// Preparing specific playlists for audio and video
					if ($temp_item->audiofile){
						$entry['file']	= 'url: "'.SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile).'"';
						unset($entry['error']);
					} else {
						if ($img){
							$entry['file']	= 'url: "'.$img.'"';
						} else {
							$entry['file']	= 'url: "media/com_sermonspeaker/images/nopict.jpg"';
						}
						$entry['error']	= 'error: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
					}
					$audios[] = implode(',', $entry);
					if ($temp_item->videofile){
						$entry['file']	= 'url: "'.SermonspeakerHelperSermonspeaker::makeLink($temp_item->videofile).'"';
						unset($entry['error']);
					} else {
						if ($img){
							$entry['file']	= 'url: "'.$img.'"';
						} else {
							$entry['file']	= 'url: "media/com_sermonspeaker/images/nopict.jpg"';
						}
						$entry['error']	= 'error: "'.JText::_('JGLOBAL_RESOURCE_NOT_FOUND').'"';
					}
					$videos[] = implode(',', $entry);
				}
			}
			if ($this->toggle){
				$this->playlist['audio'] = '{'.implode('},{', $audios).'}';
				$this->playlist['video'] = '{'.implode('},{', $videos).'}';
			}
			$this->playlist['default'] = implode('},{', $entries);
		} else {
			$option	= '';
			$js		= '';
			$type	= ($this->status == 'audio') ? 'a' : 'v';
			$this->playlist['default']	= 'url: "'.$this->file.'"';
			if ($this->toggle){
				$this->playlist['audio']	= '{url: "'.$this->playlist['audio'].'"}';
				$this->playlist['video']	= '{url: "'.$this->playlist['video'].'"}';
			}
		}
		$this->mspace = '<div style="width:'.$this->config[$type.'width'].'; height:'.$this->config[$type.'height'].'" id="mediaspace'.$this->config['count'].'"></div>';
		$this->script = '<script type="text/javascript">'
							.'flowplayer("mediaspace'.$this->config['count'].'", "'.$player.'", {'
								.'plugins: {'
									.'audio: {'
										.'url: "flowplayer.audio-3.2.9.swf"'
									.'},'
									.'controls: {'
										.$option
										.'fullscreen: false,'
										.'height: 23,'
										.'autoHide: false'
									.'}'
								.'},'
								.'clip: {'
									.'scaling: "fit",'
									.'image: false,'
									.'autoBuffering: true,'
									.'autoPlay: '.$start
								.'},'
								.$js
								.'playlist: [{'
									.$this->playlist['default']
								.'}]'
							.'});'
						.'</script>';

		// Loading needed Javascript only once
		if (!self::$fwscript){
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('function ss_play(id){flowplayer().play(parseInt(id));}');
			JHTML::Script('media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.10.min.js');
			if ($this->toggle){
				$awidth		= is_numeric($this->config['awidth']) ? $this->config['awidth'].'px' : $this->config['awidth'];
				$aheight	= is_numeric($this->config['aheight']) ? $this->config['aheight'].'px' : $this->config['aheight'];
				$vwidth		= is_numeric($this->config['vwidth']) ? $this->config['vwidth'].'px' : $this->config['vwidth'];
				$vheight	= is_numeric($this->config['vheight']) ? $this->config['vheight'].'px' : $this->config['vheight'];
				if ($this->status != 'playlist'){
					$url = 'index.php?&task=download&id='.$this->item->slug.'&type=';
					$download_video = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'video').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_VIDEO').'"';
					$download_audio = 'document.getElementById("sermon_download").onclick=function(){window.location.href=\''.JRoute::_($url.'audio').'\'};document.getElementById("sermon_download").value="'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_AUDIO').'"';
				} else {
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
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->audiofile));
				} elseif (($this->config['type'] != 'audio') && ($item->videofile && ($this->prio || ($this->config['type'] == 'video') || !$item->audiofile))){
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->videofile));
				} else {
					$files[]	= urlencode(JURI::root());
					$titles[]	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
					$artists	= '';
					continue;
				}
				$titles[]	= ($item->sermon_title) ? urlencode($item->sermon_title) : '';
				$artists[]	= ($item->name) ? urlencode($item->name) : '';
			}
			$file	= implode(',',$files);
			$title	= 'titles: "'.implode(',',$titles).'",';
			$artist	= 'artists: "'.implode(',',$artists).'",';
		} else {
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

	private function WMVPlayer(){
		$this->player = 'WMVPlayer';
		$player	= JURI::root().'media/com_sermonspeaker/player/wmvplayer/wmvplayer.xaml';
		$start	= $this->config['autostart'] ? 1 : 0;
		$type	= ($this->status == 'audio') ? 'a' : 'v';
		$this->mspace	= '<div id="mediaspace'.$this->config['count'].'">Silverlight needs Javascript turned on</div>';
		$image = SermonspeakerHelperSermonspeaker::insertPicture($this->item);
		if ($image){
			$image = "'image':'".$image."',";
		}
		$duration = '';
		if ($this->item->sermon_time != '00:00:00'){
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
		if (!self::$wmvscript){
			JHTML::Script('media/com_sermonspeaker/player/wmvplayer/silverlight.js');
			JHTML::Script('media/com_sermonspeaker/player/wmvplayer/wmvplayer.js');
			self::$wmvscript = 1;
		}
		return;
	}

	private function MediaPlayer(){
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

	private function Vimeo(){
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
		return;
	}

	// Sets the dimensions of the player for audio and video. $height and $width are default values.
	private function setDimensions($height, $width){
		if (!isset($this->config['aheight'])){
			$this->config['aheight']	= $this->params->get('aheight', $height);
		}
		if (!isset($this->config['awidth'])){
			$this->config['awidth']		= $this->params->get('awidth', $width);
		}
		if (!isset($this->config['vheight'])){
			$this->config['vheight']	= $this->params->get('vheight', '300px');
		}
		if (!isset($this->config['vwidth'])){
			$this->config['vwidth']		= $this->params->get('vwidth', '100%');
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