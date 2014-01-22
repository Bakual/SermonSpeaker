<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$type		= $params->get('sc_type');
$menuitem	= (int) $params->get('sc_menuitem');
$options = '';

if ($type)
{
	$options .= '&amp;type=' . $type;
}

if ($menuitem)
{
	$options .= '&amp;Itemid=' . $menuitem;
}

$feedFile = JURI::root() . 'index.php?option=com_sermonspeaker&amp;view=feed&amp;format=raw' . $options;

require JModuleHelper::getLayoutPath('mod_sermoncast', $params->get('layout', 'default'));
