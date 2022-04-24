<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Sermonspeaker Component Sermonspeaker Helper
 *
 * @since  3.4
 */
class JHtmlIcon
{
	/**
	 * Create link
	 *
	 * @param   object  $category  Category object
	 * @param   object  $params    Parameters
	 * @param   string  $view      Which edit view to load (sermon, serie or speaker)
	 *
	 * @return  string  Create link
	 *
	 * @since ?
	 */
	public static function create($category, $params, $view = 'sermon')
	{
		// Ignore if Frontend Uploading is disabled
		if ($params && !$params->get('fu_enable'))
		{
			return '';
		}

		// Decide on controller
		switch ($view)
		{
			case 'serie':
				$controller = 'serieform';
				break;
			case 'speaker':
				$controller = 'speakerform';
				break;
			case 'sermon':
			default:
				$view       = 'sermon';
				$controller = 'frontendupload';
				break;
		}

		$uri  = Uri::getInstance();
		$url  = 'index.php?option=com_sermonspeaker&task=' . $controller . '.add&return=' . base64_encode($uri) . '&s_id=0&catid=' . $category->id;
		$text = Text::_('JNEW') . '&#160;';

		return HTMLHelper::_('link', Route::_($url), $text, 'class="btn btn-primary"');
	}

	/**
	 * Edit link
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   array   $attribs  Attributes
	 *
	 * @return  string  Edit link
	 *
	 * @since ?
	 */
	public static function edit($item, $params, $attribs = array())
	{
		// Initialise variables.
		$user = Factory::getUser();
		$uri  = Uri::getInstance();

		// Ignore if Frontend Uploading is disabled
		if ($params && !$params->get('fu_enable'))
		{
			return '';
		}

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Ignore if the state is negative (trashed)
		if ($item->state < 0)
		{
			return '';
		}

		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out') && property_exists($item, 'checked_out_time')
			&& $item->checked_out > 0 && $item->checked_out != $user->id
		)
		{
			$checkoutUser = Factory::getUser($item->checked_out);
			$button       = HTMLHelper::_('image', 'system/checked_out.png', null, null, true);
			$date         = HTMLHelper::_('date', $item->checked_out_time);
			$tooltip      = Text::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . Text::sprintf('COM_SERMONSPEAKER_CHECKED_OUT_BY', $checkoutUser->name)
				. ' <br /> ' . $date;

			return '<span class="hasTooltip" title="' . htmlspecialchars($tooltip, ENT_COMPAT) . '">' . $button . '</span>';
		}

		switch ($attribs['type'])
		{
			default:
			case 'sermon':
				$view = 'frontendupload';
				break;
			case 'serie':
				$view = 'serieform';
				break;
			case 'speaker':
				$view = 'speakerform';
				break;
		}

		$url = 'index.php?option=com_sermonspeaker&task=' . $view . '.edit&s_id=' . $item->id . '&return=' . base64_encode($uri);

		if ($item->state == 0)
		{
			$tooltip = Text::_('JUNPUBLISHED');
		}
		else
		{
			$tooltip = Text::_('JPUBLISHED');
		}

		if ($item->created != Factory::getDbo()->getNullDate())
		{
			$date    = HTMLHelper::_('date', $item->created);
			$tooltip .= '<br>';
			$tooltip .= Text::sprintf('JGLOBAL_CREATED_DATE_ON', $date);
		}

		if ($item->author)
		{
			$tooltip .= '<br>';
			$tooltip .= Text::_('JAUTHOR') . ': ' . htmlspecialchars($item->author, ENT_COMPAT);
		}

		$icon = $item->state ? 'edit' : 'eye-slash';

		if (strtotime($item->publish_up) > strtotime(Factory::getDate())
			|| ((strtotime($item->publish_down) < strtotime(Factory::getDate())) && $item->publish_down != Factory::getDbo()->getNullDate())
		)
		{
			$icon = 'eye-slash';
		}

		$text = '<span class="hasTooltip fas fa-' . $icon . '"  title="<strong>' . Text::_('JACTION_EDIT') . '</strong><br>' . $tooltip . '"></span> ';

		if (empty($attribs['hide_text']))
		{
			$text .= Text::_('JACTION_EDIT');
		}

		return HTMLHelper::_('link', Route::_($url), $text);
	}

	/**
	 * Download link
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   array   $attribs  Attributes
	 *
	 * @return string Download link
	 *
	 * @since ?
	 */
	public static function download($item, $params, $attribs = array())
	{
		$fileurl  = Route::_('index.php?option=com_sermonspeaker&task=download&id=' . $item->id . '&type=' . $attribs['type']);
		$filesize = $attribs['type'] . 'filesize';

		if ($item->$filesize)
		{
			$size = '<span itemprop="contentSize">' . SermonspeakerHelperSermonspeaker::convertBytes($item->$filesize) . '</span>';
			$text = Text::sprintf('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $attribs['type'] . '_WITH_SIZE', $size);
		}
		else
		{
			$text = Text::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $attribs['type']);
		}

		if (empty($attribs['hideIcon']))
		{
			$text = '<span class="icon-download" > </span> ' . $text;
		}

		if ($params->get('enable_ga_events'))
		{
			$output  = '<meta itemprop="contentUrl" content="' . $fileurl . '" />';
			$onclick = "ga('send', 'event', 'SermonSpeaker Download', '" . $attribs['type'] . "', 'id:" . $item->id . "');"
				. "window.location.href='" . $fileurl . "';";
			$output  .= '<a href="#" onclick="' . $onclick . '">' . $text . '</a>';
		}
		else
		{
			$output = '<a href="' . $fileurl . '" itemprop="contentUrl">' . $text . '</a>';
		}

		return $output;
	}

	/**
	 * Play icon to control the player
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   array   $attribs  Attributes
	 *
	 * @return string Play icon
	 *
	 * @since ?
	 */
	public static function play($item, $params, $attribs = array())
	{
		if ($params->get('list_icon_function') != 2)
		{
			return '';
		}

		$text = '<i class="icon-play"> </i> ' . Text::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');

		return '<a href="#" class="ss-play" data-id="' . $attribs['index'] . '" data-player="' . $attribs['playerid'] . '" onclick="ss_play(' . $attribs['index'] . ')return false;">' . $text . '</a>';
	}
}
