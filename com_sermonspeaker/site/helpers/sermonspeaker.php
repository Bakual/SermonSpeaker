<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Sermonspeaker Component Sermonspeaker Helper
 *
 * @since  3.4
 */
class SermonspeakerHelperSermonspeaker
{
	/**
	 * @var  Joomla\Registry\Registry $params Registry object
	 *
	 * @since ?
	 */
	private static $params;

	/**
	 * @var  string $view Name of current view
	 *
	 * @since ?
	 */
	private static $view;

	/**
	 * Stores the params
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	private static function getParams()
	{
		$option = 'com_sermonspeaker';

		/* @var  $app  \Joomla\CMS\Application\SiteApplication */
		$app          = Factory::getApplication();
		self::$params = (method_exists($app, 'getParams')) ? $app->getParams($option) : ComponentHelper::getParams($option);;
	}

	/**
	 * Stores the view. Used by the insertSermonTitle method
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	private static function getView()
	{
		self::$view = Factory::getApplication()->input->get('view', 'sermons');
	}

	/**
	 * Inserts Addfile link
	 *
	 * @param   string $addfile     URL
	 * @param   string $addfileDesc Description
	 * @param   int    $show_icon   Show an icon
	 *
	 * @return  string  Addfile Link
	 *
	 * @since ?
	 */
	public static function insertAddfile($addfile, $addfileDesc, $show_icon = 0)
	{
		if (!$addfile)
		{
			return '';
		}

		$html    = '';
		$onclick = '';
		$icon    = '';

		if (!self::$params)
		{
			self::getParams();
		}

		$pos = strpos($addfile, 'icon=');

		if ($pos !== false)
		{
			$icon    = substr($addfile, $pos + 5);
			$addfile = substr($addfile, 0, $pos - 1);
		}

		$link = self::makeLink($addfile);

		if (self::$params->get('enable_ga_events'))
		{
			$onclick = " onclick=\"ga('send', 'event', 'SermonSpeaker Download', 'Additional File', '" . $addfile . "');\"";
		}

		if ($show_icon)
		{
			if (!$icon)
			{
				// Get extension of file
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

			$html .= '<a class="hasTooltip" title="::' . Text::_('COM_SERMONSPEAKER_ADDFILE_HOOVER') . '" href="'
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
					$slash       = strrpos($addfile, '/');
					$addfileDesc = ($slash !== false) ? substr($addfile, $slash + 1) : $addfile;
				}
			}

			$html .= '<a class="hasTooltip" title="::' . Text::_('COM_SERMONSPEAKER_ADDFILE_HOOVER') . '" href="'
				. $link . '"' . $onclick . ' target="_blank">' . $addfileDesc . '</a>';
		}

		return $html;
	}

	/**
	 * Creates full links, adding base path
	 *
	 * @param   string $path URL
	 * @param   bool   $abs  absolute or relative link
	 *
	 * @return  string  URL
	 *
	 * @since ?
	 */
	public static function makeLink($path, $abs = false)
	{
		if (!parse_url($path, PHP_URL_SCHEME))
		{
			$path = ($abs) ? Uri::base() . trim($path, '/') : Uri::base(true) . '/' . trim($path, '/');
		}

		return $path;
	}

	/**
	 * Inserts download button
	 *
	 * @param   int    $id   ID of the sermon
	 * @param   string $type Audio or video download
	 * @param   int    $mode Various modes
	 * @param   int    $size Filesize
	 *
	 * @return  string  button
	 *
	 * @since ?
	 */
	public static function insertdlbutton($id, $type = 'audio', $mode = 0, $size = 0)
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$id = (int) $id;

		$text    = ($size) ? Text::sprintf('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $type . '_WITH_SIZE', self::convertBytes($size))
			: Text::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $type);
		$onclick = '';
		$fileurl = Route::_('index.php?task=download&id=' . $id . '&type=' . $type);

		if ($mode == 2)
		{
			// Button with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "ga('send', 'event', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "');";
			}

			$html = '<button id="sermon_download" class="btn btn-small download_btn" onclick="' . $onclick . 'window.location.href=\'' . $fileurl . '\';" >'
				. '<i class="icon-download"> </i> ' . $text . '</button>';
		}
		elseif ($mode == 1 || $mode == 3)
		{
			// Link with Bootstrap Icon
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"ga('send', 'event', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "');\"";
			}

			$html = '<a href="' . $fileurl . '" target="_blank" ' . $onclick . ' class="hasTooltip" title="' . $text . '">'
				. '<i class="icon-download"> </i>'
				. '</a>';
		}
		elseif ($mode == 4)
		{
			// Link with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "onclick=\"ga('send', 'event', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "');\"";
			}

			$html = '<a href="' . $fileurl . '" target="_blank" ' . $onclick . ' class="download">' . $text . '</a>';
		}
		else
		{
			// Button with Text
			if (self::$params->get('enable_ga_events'))
			{
				$onclick = "ga('send', 'event', 'SermonSpeaker Download', '" . $type . "', 'id:" . $id . "');";
			}

			$html = '<input id="sermon_download" class="btn download_btn" type="button" value="' . $text . '" onclick="'
				. $onclick . 'window.location.href=\'' . $fileurl . '\';" />';
		}

		return $html;
	}

	/**
	 * Inserts popup button
	 *
	 * @param   int    $id     ID of the sermon
	 * @param   object $player Player object for popup dimensions
	 *
	 * @return  string  button
	 *
	 * @since ?
	 */
	public static function insertPopupButton($id, $player)
	{
		$html = '<input class="btn popup_btn" type="button" name="' . Text::_('COM_SERMONSPEAKER_POPUPPLAYER') . '" value="'
			. Text::_('COM_SERMONSPEAKER_POPUPPLAYER') . '" onclick="popup=window.open(\''
			. Route::_('index.php?view=sermon&layout=popup&id=' . (int) $id . '&tmpl=component') . '\', \'PopupPage\', \'height='
			. $player->popup['height'] . ',width=' . $player->popup['width'] . ',scrollbars=yes,resizable=yes\'); return false" />';

		return $html;
	}

	/**
	 * Inserts time
	 *
	 * @param   string $time Time
	 *
	 * @return  string  formatted time
	 *
	 * @since ?
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
	 * @param   int    $i      Counter
	 * @param   object $item   Sermon
	 * @param   object $player Player
	 * @param   bool   $icon   Show icon or not
	 *
	 * @return  string  title
	 *
	 * @since ?
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
			// 0: Detailpage, 1: Download, 2: Control Player, 3: Popup
			$list_icon_function = self::$params->get('list_icon_function', 3);

			if ($list_icon_function && !$item->audiofile && !$item->videofile)
			{
				$return .= '<span class="fa fa-"> </span> ';
			}
			else
			{
				switch ($list_icon_function)
				{
					case 0:
						$pic    = '<span class="fa fa-play hasTooltip" title="' . Text::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER') . '"></span>';
						$return .= HtmlHelper::link(Route::_(SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catid, $item->language)), $pic) . ' ';
						break;
					case 1:
						if (!$item->audiofile && !$item->videofile)
						{
							break;
						}

						$pic    = '<span class="fa fa-play hasTooltip" title="' . Text::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER') . '"> </span>';
						$file   = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
						$return .= HtmlHelper::link(self::makeLink($file), $pic) . ' ';
						break;
					case 2:
						if (!$item->audiofile && !$item->videofile)
						{
							break;
						}

						$cols = self::$params->get('col');

						if (!is_array($cols))
						{
							$cols = array();
						}

						if (in_array(self::$view . ':player', $cols))
						{
							$options['onclick'] = 'ss_play(' . $i . ');return false;';
							$options['title']   = Text::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
							$return             = '<span class="fa fa-play pointer hasTooltip" onclick="' . $options['onclick'] . '" title="' . $options['title'] . '"> </span> ';
						}

						break;
					case 3:
						if (!$item->audiofile && !$item->videofile)
						{
							break;
						}

						$options['onclick'] = "popup=window.open('" . Route::_('index.php?view=sermon&layout=popup&id=' . $item->id . '&tmpl=component')
							. "', 'PopupPage', 'height=" . $player->popup['height'] . ',width=' . $player->popup['width']
							. ",scrollbars=yes,resizable=yes'); return false";
						$options['title']   = Text::_('COM_SERMONSPEAKER_POPUPPLAYER');
						$return             = '<span class="fa fa-play pointer hasTooltip" onclick="' . $options['onclick'] . '" title="' . $options['title'] . '"> </span> ';
						break;
					case 4:
						break;
				}
			}
		}

		// Prepare title link function
		$options = array('class' => 'hasTooltip');

		// 0: Detailpage, 1: Download, 2: Control Player, 3: Popup
		$list_title_function = self::$params->get('list_title_function', 0);

		if ($list_title_function && !$item->audiofile && !$item->videofile)
		{
			$return .= $item->title;
		}
		else
		{
			switch (self::$params->get('list_title_function', 0))
			{
				case 0:
					$options['title'] = Text::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$return           .= HtmlHelper::link(Route::_(SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catid, $item->language)), $item->title, $options);
					break;
				case 1:
					$options['title'] = Text::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$file             = ($item->videofile && (self::$params->get('fileprio', 0) || !$item->audiofile)) ? $item->videofile : $item->audiofile;
					$return           .= HtmlHelper::link(self::makeLink($file), $item->title, $options);
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
						$options['title']   = Text::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
						$return             .= HtmlHelper::link('#', $item->title, $options);
					}
					else
					{
						$options['title'] = Text::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
						$return           .= HtmlHelper::link(Route::_(SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catid, $item->language)), $item->title, $options);
					}

					break;
				case 3:
					$options['onclick'] = "popup=window.open('" . Route::_('index.php?view=sermon&layout=popup&id=' . $item->id . '&tmpl=component')
						. "', 'PopupPage', 'height=" . $player->popup['height'] . ',width=' . $player->popup['width']
						. ",scrollbars=yes,resizable=yes'); return false";
					$options['title']   = Text::_('COM_SERMONSPEAKER_POPUPPLAYER');
					$return             .= HtmlHelper::link('#', $item->title, $options);
					break;
			}
		}

		return $return;
	}

	/**
	 * Inserts Searchtags. Based on code from Douglas Machado
	 *
	 * @param   object $item      Item
	 * @param   string $separator Separator between the tags
	 *
	 * @return  string  Searchtags
	 *
	 * @since ?
	 */
	public static function insertSearchTags($item, $separator = ', ')
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$enable_keywords = self::$params->get('enable_keywords', 0);
		$tags            = array();

		// @codingStandardsIgnoreStart
		if ($enable_keywords & 1)
		{
			// @codingStandardsIgnoreEnd
			$metakey  = (is_object($item)) ? $item->metakey : $item;
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
				$links[] = '<a href="' . Route::_('index.php?option=com_search&ordering=newest&searchphrase=all&searchword=' . $tag) . '" >' . $tag . '</a>';
			}
		}

		return implode($separator, $links);
	}

	/**
	 * Searchs for a matching picture in the order sermon > series > speaker
	 *
	 * @param   object $item     Item
	 * @param   bool   $makeLink Makes a link
	 * @param   bool   $abs      Makes the link absolute, only relevant together with $makeLink
	 *
	 * @return  string  Path to picture
	 *
	 * @since ?
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
	 * @param   string $scripture String containing the scripture
	 * @param   string $between   Delimiter
	 * @param   bool   $addTag    Adds plugin tags around scripture
	 *
	 * @return  string  Scriptures
	 *
	 * @since ?
	 */
	public static function insertScriptures($scripture, $between, $addTag = true)
	{
		if (!$scripture)
		{
			return '';
		}

		$explode    = explode('!', $scripture);
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
	 * @param   string $scripture String containing the scripture
	 * @param   bool   $addTag    Adds plugin tags around scripture
	 *
	 * @return  string  Scripture
	 *
	 * @since ?
	 */
	public static function buildScripture($scripture, $addTag = true)
	{
		if (!self::$params)
		{
			self::getParams();
		}

		$explode = explode('|', $scripture);

		if (count($explode) != 6)
		{
			return '';
		}

		$text = '';

		if ($explode[5])
		{
			$text .= $explode[5];
		}
		else
		{
			$separator = Text::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
			$text      .= Text::_('COM_SERMONSPEAKER_BOOK_' . $explode[0]);

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

				// Due to a strange behavior of the menuitem (Registry?), I manually retrieve component params if "Use Global" was selected.
				if (!$tags)
				{
					$tags = ComponentHelper::getParams('com_sermonspeaker')->get('plugin_tag');
				}

				$text = $tags[0] . $text . $tags[1];
			}
		}

		return $text;
	}

	/**
	 * Get MIME type for extension
	 *
	 * @param   string $ext File extension
	 *
	 * @return  string  MIME type
	 *
	 * @since ?
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
	 * @param   object|array $item   Can be a single sermon object or an array of sermon objects
	 * @param   array        $config Should be an array of config options. Valid options:
	 *                               - count (id of the player)
	 *                               - type (may be audio, video or auto)
	 *                               - prio (may be 0 for audio or 1 for video)
	 *                               - autostart (overwrites the backend setting)
	 *                               - alt_player (overwrites the backend setting)
	 *                               - awidth, aheight (width and height for audio)
	 *                               - vwidth, vheight (width and height for video)
	 *
	 * @return  object  Player
	 *
	 * @since ?
	 */
	public static function getPlayer($item, $config = array())
	{
		if (!is_array($config))
		{
			Factory::getApplication()->enqueueMessage('Wrong calling of getPlayer(), second parameter needs to be an array', 'warning');
			$config = array();
		}

		// Create player object to pass through plugins
		$player                  = new stdClass;
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

		PluginHelper::importPlugin('sermonspeaker');
		Factory::getApplication()->triggerEvent('onGetPlayer', array('SermonspeakerHelperSermonspeaker.getPlayer', &$player, $item, $registry));

		if (!$player->mspace)
		{
			$player->mspace = '<div class="alert">No matching player found</div>';
		}

		return $player;
	}

	/**
	 * Method to convert bytes into Megabytes or what is needed
	 *
	 * @param   object $item Item
	 * @param   bool   $prio True for audio or false for video
	 *
	 * @return  mixed  filepath or false
	 *
	 * @since ?
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
	 * @param   int  $bytes Bytes
	 * @param   bool $si    use prefix based on SI norm instead the new IEC norm
	 * @param   bool $short use short prefix
	 *
	 * @return  string  converted bytes
	 *
	 * @since ?
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
					'Yottabyte',
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
					'Yobibyte',
				);
			}

			$factor = 1024;
		}

		$count = count($unit) - 1;
		$x     = 0;
		$bytes = (int) $bytes;

		while (($bytes >= $factor) && ($x < $count))
		{
			$bytes /= $factor;
			$x++;
		}

		return number_format($bytes, 2) . ' ' . $unit[$x];
	}
}
