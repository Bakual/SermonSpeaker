<?php
defined('_JEXEC') or die;

require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player.php');

/**
 * Pixelout (WOrdpress Audio Player)
 */
class SermonspeakerHelperPlayerPixelout extends SermonspeakerHelperPlayer
{
	private static $script_loaded;

	public function isSupported($file){
		$ext		= JFile::getExt($file);
		if ($ext == 'mp3')
		{
			// Audio File
			$this->mode	= 'audio';
		}
		else
		{
			$this->mode	= false;
		}
		return $this->mode;
	}

	public function getName()
	{
		return '1 Pixel Out Audio Player';
	}

	public function preparePlayer($item, $config)
	{
		$this->config	= $config;
		$this->player	= 'PixelOut';
		$start			= $this->config['autostart'] ? 'yes' : 'no';

		if(is_array($item))
		{
			$this->mspace = '<div id="mediaspace'.$this->config['count'].'">'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH').'</div>';
			$this->setDimensions(23, '100%');
			$files		= array();
			$titles		= array();
			$artists	= array();
			foreach($item as $temp_item)
			{
				if ($temp_item->audiofile)
				{
					$files[]	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($temp_item->audiofile));
				}
				else
				{
					$files[]	= urlencode(JURI::root());
					$titles[]	= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
					$artists[]	= '';
					continue;
				}
				$titles[]	= ($temp_item->title) ? urlencode($temp_item->title) : '';
				$artists[]	= ($temp_item->speakers_title) ? urlencode($temp_item->speakers_title) : '';
			}
			$file	= implode(',',$files);
			$title	= 'titles: "'.implode(',',$titles).'",';
			$artist	= 'artists: "'.implode(',',$artists).'",';
		}
		else
		{
			$file	= urlencode(SermonspeakerHelperSermonspeaker::makeLink($item->audiofile));
			$this->mspace = '<div id="mediaspace'.$this->config['count'].'"><audio src="'.$file.'" controls="controls" preload="auto" >'.JText::_('COM_SERMONSPEAKER_PLAYER_NEEDS_FLASH').'</audio></div>';
			$this->setDimensions(23, 290);
			$title	= ($item->title) ? 'titles: "'.urlencode($item->title).'",' : '';
			$artist	= ($item->speakers_title) ? 'artists: "'.urlencode($item->speakers_title).'",' : '';
		}
		$this->script = '<script type="text/javascript">'
							.'AudioPlayer.embed("mediaspace'.$this->config['count'].'", {'
								.'soundFile: "'.$file.'",'
								.$title.$artist
								.'autostart: "'.$start.'"'
							.'})'
						.'</script>';
		$this->toggle = false;
		$this->setPopup('a');

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			JHtml::Script('media/com_sermonspeaker/player/audio_player/audio-player.js');
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'media/com_sermonspeaker/player/audio_player/player.swf", {
					width: "'.$this->config['awidth'].'",
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			self::$script_loaded = 1;
		}
		return;
	}
}