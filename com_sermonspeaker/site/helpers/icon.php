<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
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
	 * @param   object $category Category object
	 * @param   object $params   Parameters
	 * @param   string $view     Which edit view to load (sermon, serie or speaker)
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
				$view = 'sermon';
				$controller = 'frontendupload';
				break;
		}

		$uri = Uri::getInstance();
		$url = 'index.php?option=com_sermonspeaker&task=' . $controller . '.add&return=' . base64_encode($uri) . '&s_id=0&catid=' . $category->id;
		$text = '<span class="icon-plus"></span> ' . Text::_('JNEW') . '&#160;';

		$button = HtmlHelper::_('link', Route::_($url), $text, 'class="btn btn-primary"');

		return '<span class="hasTooltip" title="' . Text::_('COM_SERMONSPEAKER_BUTTON_NEW_' . $view) . '">' . $button . '</span>';
	}

	/**
	 * Email link
	 *
	 * @param   object $item    Sermon object
	 * @param   object $params  Parameters
	 * @param   array  $attribs Attributes
	 *
	 * @return  string  Email link
	 *
	 * @throws Exception
	 * @since ?
	 */
	public static function email($item, $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
		$uri = Uri::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));
		$template = Factory::getApplication()->getTemplate();
		$function = 'get' . ucfirst($attribs['type']) . 'Route';
		$link = $base . Route::_(SermonspeakerHelperRoute::$function($item->slug, $item->catid, $item->language), false);
		$url = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		$text = '<span class="icon-envelope"></span> ' . Text::_('JGLOBAL_EMAIL');

		$attribs['title'] = Text::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";

		$output = HtmlHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Edit link
	 *
	 * @param   object $item    Sermon object
	 * @param   object $params  Parameters
	 * @param   array  $attribs Attributes
	 *
	 * @return  string  Edit link
	 *
	 * @since ?
	 */
	public static function edit($item, $params, $attribs = array())
	{
		// Initialise variables.
		$user = Factory::getUser();
		$uri = Uri::getInstance();

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

		HtmlHelper::_('bootstrap.tooltip');

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out') && property_exists($item, 'checked_out_time')
			&& $item->checked_out > 0 && $item->checked_out != $user->id
		)
		{
			$checkoutUser = Factory::getUser($item->checked_out);
			$button = HtmlHelper::_('image', 'system/checked_out.png', null, null, true);
			$date = HtmlHelper::_('date', $item->checked_out_time);
			$tooltip = Text::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . Text::sprintf('COM_SERMONSPEAKER_CHECKED_OUT_BY', $checkoutUser->name)
				. ' <br /> ' . $date;

			return '<span class="hasTooltip" title="' . htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8') . '">' . $button . '</span>';
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
			$overlib = Text::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = Text::_('JPUBLISHED');
		}

		if ($item->created != Factory::getDbo()->getNullDate())
		{
			$date = HtmlHelper::_('date', $item->created);
			$overlib .= '&lt;br /&gt;';
			$overlib .= Text::sprintf('JGLOBAL_CREATED_DATE_ON', $date);
		}

		if ($item->author)
		{
			$overlib .= '&lt;br /&gt;';
			$overlib .= Text::_('JAUTHOR') . ': ' . htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8');
		}

		$icon = $item->state ? 'pencil-square-o' : 'eye-slash';

		if (strtotime($item->publish_up) > strtotime(Factory::getDate())
			|| ((strtotime($item->publish_down) < strtotime(Factory::getDate())) && $item->publish_down != Factory::getDbo()->getNullDate())
		)
		{
			$icon = 'eye-close';
		}

		$text = '<span class="hasTooltip fa fa-' . $icon . '" title="' . HtmlHelper::tooltipText(Text::_('JACTION_EDIT'), $overlib, 0, 0) . '"></span> ';

		if (empty($attribs['hide_text']))
		{
			$text .= Text::_('JACTION_EDIT');
		}

		$output = HtmlHelper::_('link', Route::_($url), $text);

		return $output;
	}

	/**
	 * Download link
	 *
	 * @param   object $item    Sermon object
	 * @param   object $params  Parameters
	 * @param   array  $attribs Attributes
	 *
	 * @return string Download link
	 *
	 * @since ?
	 */
	public static function download($item, $params, $attribs = array())
	{
		$fileurl = Route::_('index.php?task=download&id=' . $item->id . '&type=' . $attribs['type']);
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

		$text = '<i class="icon-download" > </i> ' . $text;

		if ($params->get('enable_ga_events'))
		{
			$output = '<meta itemprop="contentUrl" content="' . $fileurl . '" />';
			$onclick = "ga('send', 'event', 'SermonSpeaker Download', '" . $attribs['type'] . "', 'id:" . $item->id . "');"
				. "window.location.href='" . $fileurl . "';";
			$output .= '<a href="#" onclick="' . $onclick . '">' . $text . '</a>';
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
	 * @param   object $item    Sermon object
	 * @param   object $params  Parameters
	 * @param   array  $attribs Attributes
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
		$output = '<a href="#" onclick="ss_play(' . $attribs['index'] . ');return false;">' . $text . '</a>';

		return $output;
	}
}
