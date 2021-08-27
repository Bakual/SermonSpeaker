<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;

$type            = $params->get('sc_type');
$menuitem        = (int) $params->get('sc_menuitem');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$feedFile = 'index.php?option=com_sermonspeaker&view=feed&format=raw';

if ($type)
{
	$feedFile .= '&type=' . $type;
}

if ($menuitem)
{
	$feedFile .= '&Itemid=' . $menuitem;
}


require ModuleHelper::getLayoutPath('mod_sermoncast', $params->get('layout', 'default'));
