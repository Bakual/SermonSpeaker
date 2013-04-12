<?php
defined('_JEXEC') or die;

/**
 * Sermonspeaker Component Sermonspeaker Helper
 */
class SermonspeakerHelperSermonspeaker
{
	private static $params;
	private static $view;

	static function getParams()
	{
		self::$params = JFactory::getApplication()->getParams('com_sermonspeaker');
	}

	static function getView()
	{
		self::$view	= JFactory::getApplication()->input->get('view', 'sermons');
	}

	static function SpeakerTooltip($id, $pic, $name)
	{
		if (!self::$params)
		{
			self::getParams();
		}
		if (self::$params->get('speakerpopup', 1))
		{
			$html = '<a class="modal" href="'.JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($id).'&layout=popup&tmpl=component').'" rel="{handler: \'iframe\', size: {x: 700, y: 500}}">';
		}
		else
		{
			$html = '<a href="'.JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($id)).'">';
		}
			$html .= ($pic) ? JHtml::tooltip('<img src="'.self::makeLink($pic).'" alt="'.$name.'">', $name, '', $name) : $name;
			$html .= '</a>';

		return $html;
	}

	static function insertAddfile($addfile, $addfileDesc, $show_icon = 0)
	{
		if ($addfile)
		{
			$html		= '';
			$onclick	= '';
			$icon		= '';
			if (!self::$params)
			{
				self::getParams();
			}
			$pos	= strpos($addfile, 'icon=');
			if ($pos !== FALSE)
			{
				$icon		= substr($addfile, $pos + 5);
				$addfile	= substr($addfile, 0, $pos - 1);
			}
			$link = self::makeLink($addfile); 
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', 'Additional File', '".$addfile."']);\"";
			}
			if ($show_icon)
			{
				if (!$icon)
				{
					// Get extension of file
					jimport('joomla.filesystem.file');
					$ext = JFile::getExt($addfile);
					if (file_exists(JPATH_SITE.'/media/com_sermonspeaker/icons/'.$ext.'.png'))
					{
						$icon = 'media/com_sermonspeaker/icons/'.$ext.'.png';
					}
					else
					{
						$icon = 'media/com_sermonspeaker/icons/icon.png';
					}
				}
				$html .= '<a class="hasTip" title="::'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" '.$onclick.' target="_blank"><img src="'.$icon.'" width="18" height="20" alt="" /></a>&nbsp;';
			}
			if ($show_icon != 2)
			{
				// Show filename if no addfileDesc is set
				if (!$addfileDesc)
				{
					if ($default = self::$params->get('addfiledesc'))
					{
						$addfileDesc = $default;
					}
					else
					{
						$slash = strrpos($addfile, '/');
						$addfileDesc = ($slash !== false) ? substr($addfile, $slash + 1) : $addfile;
					}
				}
				$html .= '<a class="hasTip" title="::'.JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER').'" href="'.$link.'" '.$onclick.'target="_blank">'.$addfileDesc.'</a>';
			}
			return $html;
		}
		else
		{
			return;
		}
	}

	static function makeLink($path, $abs = false) 
	{
		if (stripos($path, 'http://') !== 0 && stripos($path, 'https://') !== 0)
		{
			$path = ($abs) ? JURI::base().trim($path, '/') : JURI::base(true).'/'.trim($path, '/');
		}

		return $path;
	}

	static function insertdlbutton($id, $type = 'audio', $mode = 0, $size = 0)
	{
		if (!self::$params)
		{
			self::getParams();
		}
		$text = ($size) ? JText::sprintf('COM_SERMONSPEAKER_DOWNLOADBUTTON_'.$type.'_WITH_SIZE', self::convertBytes($size)) : JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_'.$type);
		$onclick = '';
		$fileurl = JRoute::_('index.php?task=download&id='.$id.'&type='.$type);
		if ($mode == 1)
		{
			// Link with Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '".$type."', 'id:".$id."']);\"";
			}
			$html = '<a href="'.$fileurl.'" target="_new" '.$onclick.' class="hasTip" title="::'.$text.'">'
						.'<img src="media/com_sermonspeaker/images/download.png" alt="'.$text.'" />'
					.'</a>';
		}
		elseif ($mode == 2)
		{
			// Button with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "_gaq.push(['_trackEvent', 'SermonSpeaker Download', '".$type."', 'id:".$id."']);";
			}
			$html = '<button id="sermon_download" class="btn btn-small download_btn" onclick="'.$onclick.'window.location.href=\''.$fileurl.'\';" >'
				.'<i class="icon-download"> </i> '.$text.'</button>';
		}
		elseif ($mode == 3)
		{
			// Link with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '".$type."', 'id:".$id."']);\"";
			}
			$html = '<a href="'.$fileurl.'" target="_new" '.$onclick.' class="hasTip" title="::'.$text.'">'
						.'<i class="icon-download"> </i>'
					.'</a>';
		}
		elseif ($mode == 4)
		{
			// Link with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '".$type."', 'id:".$id."']);\"";
			}
			$html = '<a href="'.$fileurl.'" target="_new" '.$onclick.' class="download">'.$text.'</a>';
		}
		else
		{
			// Button with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "_gaq.push(['_trackEvent', 'SermonSpeaker Download', '".$type."', 'id:".$id."']);";
			}
			$html = '<input id="sermon_download" class="btn download_btn" type="button" value="'.$text.'" onclick="'.$onclick.'window.location.href=\''.$fileurl.'\';" />';
		}

		return $html;
	}

	static function insertPopupButton($id = NULL, $player)
	{
		$html = '<input class="btn popup_btn" type="button" name="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" value="'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER').'" onclick="popup=window.open(\''.JRoute::_('index.php?view=sermon&layout=popup&id='.$id.'&tmpl=component').'\', \'PopupPage\', \'height='.$player->popup['height'].',width='.$player->popup['width'].',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}

	static function insertTime($time)
	{
		$tmp = explode(':', $time);
		if ((int)$tmp[0])
		{
			return $tmp[0].':'.$tmp[1].':'.$tmp[2];
		}
		else
		{
			return $tmp[1].':'.$tmp[2];
		}
	}

	static function insertSermonTitle($i, $item, $player, $icon = true)
	{
		if (!self::$params)
		{
			self::getParams();
		}
		if (!self::$view)
		{
			self::getView();
		}
		$return = '';
		// Prepare play icon function
		$options = array();
		if ($icon)
		{
			switch (self::$params->get('list_icon_function', 3))
			{
				case 0:
					$pic = '<i class="icon-play hasTip" title="::'.JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER').'"> </i>';
					$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $pic).' ';
					break;
				case 1:
					$pic = '<i class="icon-play hasTip" title="::'.JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER').'"> </i>';
					$file = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
					$return .= JHtml::Link(self::makeLink($file), $pic).' ';
					break;
				case 2:
					$cols = self::$params->get('col');
					if (!is_array($cols)){
						$cols = array();
					}
					if(in_array(self::$view.':player', $cols)){
						$options['onclick'] = 'ss_play('.$i.');return false;';
						$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
						$return = '<i class="icon-play pointer hasTip" onclick="'.$options['onclick'].'" title="'.$options['title'].'"> </i> ';
					}
					break;
				case 3:
					$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
					$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
					$options['class'] = 'icon_play pointer hasTip';
					$return .= JHtml::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_POPUPPLAYER'), $options).' ';
					break;
				case 4:
					break;
			}
		}
		// Prepare title link function
		$options = array('class' => 'hasTip');
		switch (self::$params->get('list_title_function', 0))
		{
			case 0:
				$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
				break;
			case 1:
				$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$file = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
				$return .= JHtml::Link(self::makeLink($file), $item->sermon_title, $options);
				break;
			case 2:
				$cols = self::$params->get('col');
				if (!is_array($cols)){
					$cols = array();
				}
				if(in_array(self::$view.':player', $cols)){
					$options['onclick'] = 'ss_play('.$i.');return false;';
					$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$return .= JHtml::Link('#', $item->sermon_title, $options);
				} else {
					$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
				}
				break;
			case 3:
				$options['onclick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$player->popup['height'].',width='.$player->popup['width'].",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = '::'.JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$return .= JHtml::Link('#', $item->sermon_title, $options);
				break;
		}
		return $return;
	}

	static function insertSearchTags($item, $separator = ', ')
	{
		// Based on code from Douglas Machado
		if (!self::$params)
		{
			self::getParams();
		}

		$enable_keywords = self::$params->get('enable_keywords', 0);
		$tags = array();
		if($enable_keywords&1)
		{
			$metakey = (is_object($item)) ? $item->metakey : $item;
			$keywords = explode(',', $metakey);
			foreach($keywords as $keyword)
			{
				$tags[] = trim($keyword);
			}
		}
		if ($enable_keywords > 1 && is_object($item))
		{
			$tags = array_merge($tags, $item->tags);
		}
		$tags = array_unique($tags);
		natcasesort($tags);

		$links = array();
		foreach ($tags as $tag)
		{
			if ($tag)
			{
				$links[] ='<a href="'.JRoute::_('index.php?option=com_search&ordering=newest&searchphrase=all&searchword='.$tag).'" >'.$tag.'</a>';
			}
		}
		return implode($separator, $links);
	}

	static function insertPicture($item, $makeLink = 0)
	{
		if (isset($item->picture) && $item->picture)
		{
			$image = $item->picture;
		} 
		elseif (isset($item->avatar) && $item->avatar)
		{
			$image = $item->avatar;
		}
		elseif (isset($item->pic) && $item->pic)
		{
			$image = $item->pic;
		}
		else
		{
			return false;
		}

		return ($makeLink) ? self::makeLink($image) : trim($image, '/');
	}

	static function insertScriptures($scripture, $between, $addTag = true)
	{
		if (!$scripture)
		{
			return;
		}
		$explode = explode('!', $scripture);
		$scriptures = array();
		foreach ($explode as $passage)
		{
			$scriptures[] = self::buildScripture($passage, $addTag);
		}

		return implode($between, $scriptures);
	}

	static function buildScripture($scripture, $addTag = true)
	{
		if(!self::$params)
		{
			self::getParams();
		}
		$explode = explode('|', $scripture);
		$text = '';
		if ($explode[5])
		{
			$text .= $explode[5];
		}
		else
		{
			$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
			$text .= JText::_('COM_SERMONSPEAKER_BOOK_'.$explode[0]);
			if ($explode[1])
			{
				$text .= ' '.$explode[1];
				if ($explode[2])
				{
					$text .= $separator.$explode[2];
				}
				if ($explode[3] || $explode[4])
				{
					$text .= '-';
					if ($explode[3])
					{
						$text .= $explode[3];
						if ($explode[4])
						{
							$text .= $separator.$explode[4];
						}
					}
					else
					{
						$text .= $explode[4];
					}
				}
			}
			if ($text && $addTag)
			{
				$tags = self::$params->get('plugin_tag');
				$text = $tags[0].$text.$tags[1];
			}
		}

		return $text;
	}

	static function getMime($ext)
	{
		switch ($ext)
		{
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

	/**
	 * Loading the correct playerclass and defining some default config
	 * Takes two arguments:
	 * $item can be a single sermon object or an array of sermon objects
	 * $config should be an array of config options. Valid options:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 */
	static function getPlayer($item, $config = array())
	{
		if (!is_array($config))
		{
			JError::raiseWarning(100, 'Wrong calling of getPlayer(), second parameter needs to be an array');
			$config = array();
		}

		if (!self::$params)
		{
			self::getParams();
		}

		// Setting default values
		$config['count']	= (isset($config['count'])) ? $config['count'] : 1;

		// Allow a fixed value for the type; may be audio, video or auto. "Auto" is default behaviour and takes care of the "prio" param.
		$config['type']		= (isset($config['type'])) ? $config['type'] : 'auto';
		$config['prio']		= (isset($config['prio'])) ? $config['prio'] : self::$params->get('fileprio', 0);

		// Autostart parameter may be overridden by a layout (eg for Series/Sermon View)
		$config['autostart'] = (isset($config['autostart'])) ? $config['autostart'] : self::$params->get('autostart');

		// Allow a player to be chosen by the layout (eg for icon layout); 0 = JWPlayer, 1 = PixelOut, 2 = FlowPlayer
		$config['alt_player'] = (isset($config['alt_player'])) ? $config['alt_player'] : self::$params->get('alt_player');

		// Backward compatibility for layouts (params are changed with script)
		if (is_numeric($config['alt_player']))
		{
			switch ($config['alt_player'])
			{
				case 1:
					$config['alt_player'] = 'pixelout';
					break;
				case 2:
					$config['alt_player'] = 'flowplayer3';
					break;
				case 0:
				default:
					$config['alt_player'] = 'jwplayer5';
					break;
			}
		}

		// Dispatching
		jimport('joomla.filesystem.file');
		if (!JFile::exists(JPATH_SITE.'/components/com_sermonspeaker/helpers/player/'.$config['alt_player'].'.php'))
		{
			$config['alt_player'] = 'jwplayer5';
		}
		require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player/'.$config['alt_player'].'.php');
		$classname	= 'SermonspeakerHelperPlayer'.ucfirst($config['alt_player']);
		$player		= new $classname();

		if (is_array($item))
		{
			$player->preparePlayer($item, $config);
			return $player;
		}
		else
		{
			// Detect file to use
			if ($config['type'] == 'auto')
			{
				$file	= self::getFileByPrio($item, $config['prio']);
			}
			else
			{
				$file	= ($config['type'] == 'video') ? $item->videofile : $item->audiofile;
			}
			if (!$file)
			{
				// Nothing available
				$player->popup['height']	= 0;
				$player->popup['width']		= 0;
				$player->error				= JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
				$player->toggle				= false;
				return $player;
			}
			$file	= self::makeLink($file);
			// Check if filetype is suported
			if ($player->isSupported($file))
			{
				// Prepare player
				$player->preparePlayer($item, $config);
				return $player;
			}
			else
			{
				// Try with JW Player
				if ($config['alt_player'] != 'jwplayer5')
				{
					require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player/jwplayer5.php');
					$player		= new SermonspeakerHelperPlayerJwplayer5();
					if ($player->isSupported($file))
					{
						$config['alt_player'] = 'jwplayer5';
						// Prepare player
						$player->preparePlayer($item, $config);
						return $player;
					}
				}

				// Try to find a fallback
				jimport('joomla.filesystem.folder');
				$classfiles	= JFolder::files(JPATH_SITE.'/components/com_sermonspeaker/helpers/player', '^[^_]*\.php$', false, true);
				foreach ($classfiles as $classfile)
				{
					$playername	= JFile::stripExt(JFile::getName($classfile));
					if ($playername == 'jwplayer5' || $playername == $config['alt_player'])
					{
						continue;
					}
					require_once($classfile);
					$classname	= 'SermonspeakerHelperPlayer'.ucfirst($playername);
					$player		= new $classname();
					if ($player->isSupported($file))
					{
						$config['alt_player'] = $playername;
						// Prepare player
						$player->preparePlayer($item, $config);
						return $player;
					}
				}

				$player->popup['height']	= 0;
				$player->popup['width']		= 0;
				$player->error				= 'Unsupported Filetype';
				$player->toggle				= false;
				return $player;
			}
		}
	}

	static function getFileByPrio($item, $prio)
	{
		if ($item->audiofile && (!$prio || !$item->videofile))
		{
			return $item->audiofile;
		}
		elseif ($item->videofile && ($prio || !$item->audiofile))
		{
			return $item->videofile;
		}
		return false;
	}

	/* Based on function "binary_multiples" from Damir Enseleit <info@selfphp.de> 
	 * $si		use prefix based on SI norm instead the new IEC norm
	 * $short	use short prefix
	*/
	static function convertBytes($bytes, $si = true, $short = true)
	{
		if ($si)
		{
			if ($short)
			{
				$unit = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			}
			else
			{
				$unit = array('Byte', 
							'Kilobyte', 
							'Megabyte', 
							'Gigabyte', 
							'Terabyte', 
							'Petabyte', 
							'Exabyte', 
							'Zettabyte', 
							'Yottabyte'
							);
			}
			$factor = 1000;
		}
		else
		{
			if ($short)
			{
				$unit = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
			}
			else
			{
				$unit = array('Byte', 
							'Kibibyte', 
							'Mebibyte', 
							'Gibibyte', 
							'Tebibyte', 
							'Pebibyte', 
							'Exbibyte', 
							'Zebibyte', 
							'Yobibyte'
							);
			}
			$factor = 1024;
		}
		$count = count($unit) - 1;
		$x = 0;
		$bytes = (int)$bytes;
		while (($bytes >= $factor) && ($x < $count)) 
		{
			$bytes /= $factor; 
			$x++;
		}
		return number_format($bytes, 2).' '.$unit[$x];
	}	
}