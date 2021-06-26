<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Initialise variables.
$item     = $displayData['item'];
$params   = $displayData['params'];
$type     = $displayData['type'] ?: '';
$hideText = $displayData['hide_text'] ?: '';

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
if ($item->checked_out > 0 && $item->checked_out != Factory::getApplication()->getIdentity()->id)
{
	$checkoutUser = Factory::getUser($item->checked_out);
	$date         = HTMLHelper::_('date', $item->checked_out_time);
	$tooltip      = Text::sprintf('COM_SERMONSPEAKER_CHECKED_OUT_BY', $checkoutUser->name) . ' <br /> ' . $date;

	echo '<span class="hasTooltip icon-lock" title="' . $tooltip . '"></span>';

	return;
}

switch ($type)
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

$url = 'index.php?option=com_sermonspeaker&task=' . $view . '.edit&s_id=' . $item->id . '&return=' . base64_encode(Uri::getInstance());

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

if (!$hideText)
{
	$text .= Text::_('JACTION_EDIT');
}
?>
<a href="<?php echo $url; ?>"><?php echo $text; ?></a>
