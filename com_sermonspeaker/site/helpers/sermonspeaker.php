<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Sermonspeaker Component Sermonspeaker Helper
 *
 * @since  3.4
 */
class SermonspeakerHelperSermonspeaker
{
	/**
	 * @var  Joomla\Registry\Registry  $params  Registry object
	 */
	private static $params;

	/**
	 * @var  string  $view  Name of current view
	 */
	private static $view;

	/**
	 * Stores the params
	 *
	 * @return  void
	 */
	private static function getParams()
	{
		$option = 'com_sermonspeaker';

		/* @var  $app  JApplicationSite */
		$app = JFactory::getApplication();
		self::$params = (method_exists($app, 'getParams')) ? $app->getParams($option) : JComponentHelper::getParams($option);;
	}

	/**
	 * Stores the view. Used by the insertSermonTitle method
	 *
	 * @return  void
	 */
	private static function getView()
	{
		self::$view = JFactory::getApplication()->input->get('view', 'sermons');
	}

	/**
	 * Inserts tooltip for speaker
	 *
	 * @param   int     $id             ID of the speaker
	 * @param   string  $pic            URL to picture
	 * @param   string  $speaker_title  Speaker name
	 *
	 * @deprecated  5.2  Use "JLayoutHelper::render('titles.speaker', array('item' => $this->item, 'params' => $this->params))" instead
	 *
	 * @return  string  Tooltip
	 */
	public static function SpeakerTooltip($id, $pic, $speaker_title)
	{
		if (!self::$params)
		{
			self::getParams();
		}

		// BC Code to call the layout instead
		$item = new stdclass;
		$item->speaker_title = $speaker_title;
		$item->speaker_slug  = $id;
		$item->pic           = $pic;

		return JLayoutHelper::render('titles.speaker', array('item' => $item, 'params' => self::$params, 'legacy' => true));
	}

	/**
	 * Inserts Addfile link
	 *
	 * @param   string  $addfile      URL
	 * @param   string  $addfileDesc  Description
	 * @param   int     $show_icon    Show an icon
	 *
	 * @return  string  Addfile Link
	 */
	public static function insertAddfile($addfile, $addfileDesc, $show_icon = 0)
	{
		if (!$addfile)
		{
			return '';
		}

		$html		= '';
		$onclick	= '';
		$icon		= '';

		if (!self::$params)
		{
			self::getParams();
		}

		$pos	= strpos($addfile, 'icon=');

		if ($pos !== false)
		{
			$icon		= substr($addfile, $pos + 5);
			$addfile	= substr($addfile, 0, $pos - 1);
		}

		$link = self::makeLink($addfile);

		if (self::$params->get('enable_ga_events'))
		{
			$onclick = " onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', 'Additional File', '" . $addfile . "']);\"";
		}

		if ($show_icon)
		{
			if (!$icon)
			{
				// Get extension of file
				jimport('joomla.filesystem.file');
				$ext = JFile::getExt($addfile);

				if (file_exists(JPATH_SITE . '/media/com_sermonspeaker/icons/' . $ext . '.png'))
				{
					$icon = 'media/com_sermonspeaker/icons/' . $ext . '.png';
				}
				else
				{
					$icon = 'media/com_sermonspeaker/icons/icon.png';
				}
			}

			$html .= '<a class="hasTooltip" title="::' . JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER') . '" href="'
				. $link . '" ' . $onclick . ' target="_blank"><img src="' . $icon . '" width="18" height="20" alt="" /></a>&nbsp;';
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

			$html .= '<a class="hasTooltip" title="::' . JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER') . '" href="'
				. $link . '"' . $onclick . ' target="_blank">' . $addfileDesc . '</a>';
		}

		return $html;
	}

	/**
	 * Creates full links, adding base path
	 *
	 * @param   string  $path  URL
	 * @param   bool    $abs   absolute or relative link
	 *
	 * @return  string  URL
	 */
	public static function makeLink($path, $abs = false)
	{
		if (!parse_url($path, PHP_URL_SCHEME))
		{
			$path = ($abs) ? JURI::base() . trim($path, '/') : JURI::base(true) . '/' . trim($path, '/');
		}

		return $path;
	}

	/**
	 * Inserts download button
	 *
	 * @param   int     $id    ID of the sermon
	 * @param   string  $type  Audio or video download
	 * @param   int     $mode  Various modes
	 * @param   int     $size  Filesize
	 *
	 * @return  string  button
	 */
	public static function insertdlbutton($id, $type = 'audio', $mode = 0, $size = 0)
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$id = (int) $id;

		$text = ($size) ? JText::sprintf('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $type . '_WITH_SIZE', self::convertBytes($size))
						: JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $type);
		$onclick = '';
		$fileurl = JRoute::_('index.php?task=download&id=' . $id . '&type=' . $type);

		if ($mode == 1)
		{
			// Link with Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "']);\"";
			}

			$html = '<a href="' . $fileurl . '" target="_new" ' . $onclick . ' class="hasTooltip" title="::' . $text . '">'
						. '<img src="media/com_sermonspeaker/images/download.png" alt="' . $text . '" />'
					. '</a>';
		}
		elseif ($mode == 2)
		{
			// Button with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "_gaq.push(['_trackEvent', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "']);";
			}

			$html = '<button id="sermon_download" class="btn btn-small download_btn" onclick="' . $onclick . 'window.location.href=\'' . $fileurl . '\';" >'
				. '<i class="icon-download"> </i> ' . $text . '</button>';
		}
		elseif ($mode == 3)
		{
			// Link with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "']);\"";
			}

			$html = '<a href="' . $fileurl . '" target="_new" ' . $onclick . ' class="hasTooltip" title="' . $text . '">'
						. '<i class="icon-download"> </i>'
					. '</a>';
		}
		elseif ($mode == 4)
		{
			// Link with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"_gaq.push(['_trackEvent', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "']);\"";
			}

			$html = '<a href="' . $fileurl . '" target="_new" ' . $onclick . ' class="download">' . $text . '</a>';
		}
		else
		{
			// Button with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "_gaq.push(['_trackEvent', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "']);";
			}

			$html = '<input id="sermon_download" class="btn download_btn" type="button" value="' . $text . '" onclick="'
				. $onclick . 'window.location.href=\'' . $fileurl . '\';" />';
		}

		return $html;
	}

	/**
	 * Inserts popup button
	 *
	 * @param   int     $id      ID of the sermon
	 * @param   object  $player  Player object for popup dimensions
	 *
	 * @return  string  button
	 */
	public static function insertPopupButton($id, $player)
	{
		$html = '<input class="btn popup_btn" type="button" name="' . JText::_('COM_SERMONSPEAKER_POPUPPLAYER') . '" value="'
			. JText::_('COM_SERMONSPEAKER_POPUPPLAYER') . '" onclick="popup=window.open(\''
			. JRoute::_('index.php?view=sermon&layout=popup&id=' . (int) $id . '&tmpl=component') . '\', \'PopupPage\', \'height='
			. $player->popup['height'] . ',width=' . $player->popup['width'] . ',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}

	/**
	 * Inserts time
	 *
	 * @param   string  $time  Time
	 *
	 * @return  string  formatted time
	 */
	public static function insertTime($time)
	{
		$tmp = explode(':', $time);

		if ((int) $tmp[0])
		{
			return $tmp[0] . ':' . $tmp[1] . ':' . $tmp[2];
		}
		else
		{
			return $tmp[1] . ':' . $tmp[2];
		}
	}

	/**
	 * Inserts sermon title
	 *
	 * @param   int     $i       Counter
	 * @param   object  $item    Sermon
	 * @param   object  $player  Player
	 * @param   bool    $icon    Show icon or not
	 *
	 * @return  string  title
	 */
	public static function insertSermonTitle($i, $item, $player, $icon = true)
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
					$pic = '<i class="icon-play hasTooltip" title="::' . JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER') . '"> </i>';
					$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $pic) . ' ';
					break;
				case 1:
					$pic = '<i class="icon-play hasTooltip" title="::' . JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER') . '"> </i>';
					$file = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
					$return .= JHtml::Link(self::makeLink($file), $pic) . ' ';
					break;
				case 2:
					$cols = self::$params->get('col');

					if (!is_array($cols))
					{
						$cols = array();
					}

					if (in_array(self::$view . ':player', $cols))
					{
						$options['onclick'] = 'ss_play(' . $i . ');return false;';
						$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
						$return = '<i class="icon-play pointer hasTooltip" onclick="' . $options['onclick'] . '" title="' . $options['title'] . '"> </i> ';
					}

					break;
				case 3:
					$options['onclick'] = "popup=window.open('" . JRoute::_('index.php?view=sermon&layout=popup&id=' . $item->id . '&tmpl=component')
						. "', 'PopupPage', 'height=" . $player->popup['height'] . ',width=' . $player->popup['width']
						. ",scrollbars=yes,resizable=yes'); return false";
					$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
					$options['class'] = 'icon_play pointer hasTooltip';
					$return .= JHtml::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_POPUPPLAYER'), $options) . ' ';
					break;
				case 4:
					break;
			}
		}

		// Prepare title link function
		$options = array('class' => 'hasTooltip');

		switch (self::$params->get('list_title_function', 0))
		{
			case 0:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->title, $options);
				break;
			case 1:
				$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
				$file = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
				$return .= JHtml::Link(self::makeLink($file), $item->title, $options);
				break;
			case 2:
				$cols = self::$params->get('col');

				if (!is_array($cols))
				{
					$cols = array();
				}

				if (in_array(self::$view . ':player', $cols))
				{
					$options['onclick'] = 'ss_play(' . $i . ');return false;';
					$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$return .= JHtml::Link('#', $item->title, $options);
				}
				else
				{
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$return .= JHtml::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->title, $options);
				}

				break;
			case 3:
				$options['onclick'] = "popup=window.open('" . JRoute::_('index.php?view=sermon&layout=popup&id=' . $item->id . '&tmpl=component')
					. "', 'PopupPage', 'height=" . $player->popup['height'] . ',width=' . $player->popup['width']
					. ",scrollbars=yes,resizable=yes'); return false";
				$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
				$return .= JHtml::Link('#', $item->title, $options);
				break;
		}

		return $return;
	}

	/**
	 * Inserts Searchtags. Based on code from Douglas Machado
	 *
	 * @param   object  $item       Item
	 * @param   string  $separator  Separator between the tags
	 *
	 * @return  string  Searchtags
	 */
	public static function insertSearchTags($item, $separator = ', ')
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$enable_keywords = self::$params->get('enable_keywords', 0);
		$tags = array();

		// @codingStandardsIgnoreStart
		if ($enable_keywords&1)
		{
			// @codingStandardsIgnoreEnd
			$metakey = (is_object($item)) ? $item->metakey : $item;
			$keywords = explode(',', $metakey);

			foreach ($keywords as $keyword)
			{
				$tags[] = trim($keyword);
			}
		}

		// @codingStandardsIgnoreStart
		if ($enable_keywords > 1 && is_object($item))
		{
			// @codingStandardsIgnoreEnd
			foreach ($item->tags->itemTags as $tag)
			{
				$tags[] = $tag->title;
			}
		}

		$tags = array_unique($tags);
		natcasesort($tags);
		$links = array();

		foreach ($tags as $tag)
		{
			if ($tag)
			{
				$links[] = '<a href="' . JRoute::_('index.php?option=com_search&ordering=newest&searchphrase=all&searchword=' . $tag) . '" >' . $tag . '</a>';
			}
		}

		return implode($separator, $links);
	}

	/**
	 * Searchs for a matching picture in the order sermon > series > speaker
	 *
	 * @param   object  $item      Item
	 * @param   bool    $makeLink  Makes a link
	 * @param   bool    $abs       Makes the link absolute, only relevant together with $makeLink
	 *
	 * @return  string  Path to picture
	 */
	public static function insertPicture($item, $makeLink = false, $abs = false)
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$pictures = array();

		switch (self::$params->get('picture_prio', 0))
		{
			case 0:
				$pictures = array('picture', 'avatar', 'pic');
				break;
			case 1:
				$pictures = array('picture', 'avatar');
				break;
			case 2:
				$pictures = array('picture', 'pic', 'avatar');
				break;
			case 3:
				$pictures = array('picture', 'pic');
				break;
		}

		foreach ($pictures as $pic)
		{
			if (empty($item->$pic))
			{
				continue;
			}

			return ($makeLink) ? self::makeLink($item->$pic, $abs) : trim($item->$pic, '/');
		}

		return '';
	}

	/**
	 * Inserting the scriptures
	 *
	 * @param   string  $scripture  String containing the scripture
	 * @param   string  $between    Delimiter
	 * @param   bool    $addTag     Adds plugin tags around scripture
	 *
	 * @return  string  Scriptures
	 */
	public static function insertScriptures($scripture, $between, $addTag = true)
	{
		if (!$scripture)
		{
			return '';
		}

		$explode = explode('!', $scripture);
		$scriptures = array();

		foreach ($explode as $passage)
		{
			$scriptures[] = self::buildScripture($passage, $addTag);
		}

		return implode($between, $scriptures);
	}

	/**
	 * Building the scripture
	 *
	 * @param   string  $scripture  String containing the scripture
	 * @param   bool    $addTag     Adds plugin tags around scripture
	 *
	 * @return  string  Scripture
	 */
	public static function buildScripture($scripture, $addTag = true)
	{
		if (!self::$params)
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
			$separator = JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
			$text .= JText::_('COM_SERMONSPEAKER_BOOK_' . $explode[0]);

			if ($explode[1])
			{
				$text .= ' ' . $explode[1];

				if ($explode[2])
				{
					$text .= $separator . $explode[2];
				}

				if ($explode[3] || $explode[4])
				{
					$text .= '-';

					if ($explode[3])
					{
						$text .= $explode[3];

						if ($explode[4])
						{
							$text .= $separator . $explode[4];
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
				$text = $tags[0] . $text . $tags[1];
			}
		}

		return $text;
	}

	/**
	 * Get MIME type for extension
	 *
	 * @param   string  $ext  File extension
	 *
	 * @return  string  MIME type
	 */
	public static function getMime($ext)
	{
		switch ($ext)
		{
			case 'mp3':
				$mime = 'audio/mpeg';
				break;
			case 'aac':
				$mime = 'audio/aac';
				break;
			case 'm4a':
				$mime = 'audio/mp4a-latm';
				break;
			case 'flv':
				$mime = 'video/x-flv';
				break;
			case 'mp4':
			case 'f4v':
				$mime = 'video/mp4';
				break;
			case 'm4v':
				$mime = 'video/m4v';
				break;
			case 'mov':
				$mime = 'video/quicktime';
				break;
			case '3gp':
				$mime = 'video/3gpp';
				break;
			case '3g2':
				$mime = 'video/3gpp2';
				break;
			case 'pdf':
				$mime = 'application/pdf';
				break;
			default:
				$mime = 'video/mp4';
				break;
		}

		return $mime;
	}

	/**
	 * Loading the correct playerclass and defining some default config
	 *
	 * @param   object|array  $item    Can be a single sermon object or an array of sermon objects
	 * @param   array         $config  Should be an array of config options. Valid options:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 *
	 * @return  object  Player
	 */
	public static function getPlayer($item, $config = array())
	{
		if (!is_array($config))
		{
			JFactory::getApplication()->enqueueMessage('Wrong calling of getPlayer(), second parameter needs to be an array', 'warning');
			$config = array();
		}

		if (!self::$params)
		{
			self::getParams();
		}

		// Use Plugin
		if (!self::$params->get('alt_player'))
		{
			// Create player object to pass through plugins
			$player = new stdClass;
			$player->popup['height'] = 0;
			$player->popup['width']  = 0;
			$player->error           = '';
			$player->toggle          = false;
			$player->script          = '';
			$player->player          = '';
			$player->mspace          = '';

			// Convert $config to an Registry object
			$registry = new Joomla\Registry\Registry;
			$registry->loadArray($config);

			JPluginHelper::importPlugin('sermonspeaker');
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onGetPlayer', array('SermonspeakerHelperSermonspeaker.getPlayer', &$player, $item, $registry));

			if (!$player->mspace)
			{
				$player->mspace = '<div class="alert">No matching player found</div>';
			}

			return $player;
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

		if (!JFile::exists(JPATH_SITE . '/components/com_sermonspeaker/helpers/player/' . $config['alt_player'] . '.php'))
		{
			$config['alt_player'] = 'jwplayer5';
		}

		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player/' . $config['alt_player'] . '.php';
		$classname = 'SermonspeakerHelperPlayer' . ucfirst($config['alt_player']);

		/* @var  SermonspeakerHelperPlayerJwplayer5  $player  Default player class is JW Player, but can be any other */
		$player    = new $classname;

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
				$file = self::getFileByPrio($item, $config['prio']);
			}
			else
			{
				$file = ($config['type'] == 'video') ? $item->videofile : $item->audiofile;
			}

			if (!$file)
			{
				// Nothing available
				$player->popup['height'] = 0;
				$player->popup['width']  = 0;
				$player->error           = JText::_('JGLOBAL_RESOURCE_NOT_FOUND');
				$player->toggle          = false;

				return $player;
			}

			$file = self::makeLink($file);

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
					require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player/jwplayer5.php';
					$player = new SermonspeakerHelperPlayerJwplayer5;

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
				$classfiles = JFolder::files(JPATH_SITE . '/components/com_sermonspeaker/helpers/player', '^[^_]*\.php$', false, true);

				foreach ($classfiles as $classfile)
				{
					$playername = JFile::stripExt(basename($classfile));

					if ($playername == 'jwplayer5' || $playername == $config['alt_player'])
					{
						continue;
					}

					require_once $classfile;
					$classname = 'SermonspeakerHelperPlayer' . ucfirst($playername);
					$player    = new $classname;

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

	/**
	 * Method to convert bytes into Megabytes or what is needed
	 *
	 * @param   object  $item  Item
	 * @param   bool    $prio  True for audio or false for video
	 *
	 * @return  mixed  filepath or false
	 */
	public static function getFileByPrio($item, $prio)
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

	/**
	 * Method to convert bytes into Megabytes or what is needed
	 * Based on function "binary_multiples" from Damir Enseleit <info@selfphp.de>
	 *
	 * @param   int   $bytes  Bytes
	 * @param   bool  $si     use prefix based on SI norm instead the new IEC norm
	 * @param   bool  $short  use short prefix
	 *
	 * @return  string  converted bytes
	 */
	public static function convertBytes($bytes, $si = true, $short = true)
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
		$bytes = (int) $bytes;

		while (($bytes >= $factor) && ($x < $count))
		{
			$bytes /= $factor;
			$x++;
		}

		return number_format($bytes, 2) . ' ' . $unit[$x];
	}
}
