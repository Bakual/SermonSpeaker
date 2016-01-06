<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

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

		$uri = JURI::getInstance();
		$url = 'index.php?option=com_sermonspeaker&task=' . $controller . '.add&return=' . base64_encode($uri) . '&s_id=0&catid=' . $category->id;
		$text = '<span class="icon-plus"></span> ' . JText::_('JNEW') . '&#160;';

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="btn btn-primary"');

		return '<span class="hasTooltip" title="' . JText::_('COM_SERMONSPEAKER_BUTTON_NEW_' . $view) . '">' . $button . '</span>';
	}

	/**
	 * Email link
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   array   $attribs  Attributes
	 *
	 * @return  string  Email link
	 */
	public static function email($item, $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
		$uri      = JURI::getInstance();
		$base     = $uri->toString(array('scheme', 'host', 'port'));
		$template = JFactory::getApplication()->getTemplate();
		$function = 'get' . ucfirst($attribs['type']) . 'Route';
		$link     = $base . JRoute::_(SermonspeakerHelperRoute::$function($item->slug, $item->catid, $item->language), false);
		$url      = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		$text = '<span class="icon-envelope"></span> ' . JText::_('JGLOBAL_EMAIL');

		$attribs['title']   = JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Edit link
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   array   $attribs  Attributes
	 *
	 * @return  string  Edit link
	 */
	public static function edit($item, $params, $attribs = array())
	{
		// Initialise variables.
		$user   = JFactory::getUser();
		$uri    = JUri::getInstance();

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

		JHtml::_('bootstrap.tooltip');

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out') && property_exists($item, 'checked_out_time')
			&& $item->checked_out > 0 && $item->checked_out != $user->get('id'))
		{
			$checkoutUser = JFactory::getUser($item->checked_out);
			$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
			$date = JHtml::_('date', $item->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::sprintf('COM_SERMONSPEAKER_CHECKED_OUT_BY', $checkoutUser->name)
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
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		if ($item->created != JFactory::getDbo()->getNullDate())
		{
			$date = JHtml::_('date', $item->created);
			$overlib .= '&lt;br /&gt;';
			$overlib .= JText::sprintf('JGLOBAL_CREATED_DATE_ON', $date);
		}

		if ($item->author)
		{
			$overlib .= '&lt;br /&gt;';
			$overlib .= JText::_('JAUTHOR') . ': ' . htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8');
		}

		$icon = $item->state ? 'edit' : 'eye-close';

		if (strtotime($item->publish_up) > strtotime(JFactory::getDate())
			|| ((strtotime($item->publish_down) < strtotime(JFactory::getDate())) && $item->publish_down != JFactory::getDbo()->getNullDate()))
		{
			$icon = 'eye-close';
		}

		$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="' . JHtml::tooltipText(JText::_('JACTION_EDIT'), $overlib, 0, 0) . '"></span> '
				. JText::_('JGLOBAL_EDIT');

		$output = JHtml::_('link', JRoute::_($url), $text);

		return $output;
	}

	/**
	 * Download link
	 *
	 * @param   object  $item     Sermon object
	 * @param   object  $params   Parameters
	 * @param   object  $attribs  Attributes
	 *
	 * @return  string  Download link
	 */
	public static function download($item, $params, $attribs = array())
	{
		$onclick  = '';
		$fileurl  = JRoute::_('index.php?task=download&id=' . $item->id . '&type=' . $attribs['type']);
		$filesize = $attribs['type'] . 'filesize';

		if ($item->$filesize)
		{
			$size = '<span itemprop="contentSize">' . SermonspeakerHelperSermonspeaker::convertBytes($item->$filesize) . '</span>';
			$text = JText::sprintf('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $attribs['type'] . '_WITH_SIZE', $size);
		}
		else
		{
			$text = JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON_' . $attribs['type']);
		}

		$text = '<i class="icon-download" > </i> ' . $text;

		if ($params->get('enable_ga_events'))
		{
			$output = '<meta itemprop="contentUrl" content="' . $fileurl . '" />';
			$onclick = "ga('send', 'event', 'SermonSpeaker Download', '" . $attribs['type'] . "', 'id:" . $item->id . "');"
					. "window.location.href='" . $fileurl . "';";
			$output = '<a href="#" onclick="' . $onclick . '">' . $text . '</a>';
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
	 * @param   object  $attribs  Attributes
	 *
	 * @return  string  Play icon
	 */
	public static function play($item, $params, $attribs = array())
	{
		if ($params->get('list_icon_function') != 2)
		{
			return;
		}

		$text   = '<i class="icon-play"> </i> ' . JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
		$output = '<a href="#" onclick="ss_play(' . $attribs['index'] . ');return false;">' . $text . '</a>';

		return $output;
	}
}
