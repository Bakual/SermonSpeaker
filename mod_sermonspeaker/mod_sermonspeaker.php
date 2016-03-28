<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once __DIR__ . '/helper.php';

$list = modSermonspeakerHelper::getList($params);

if (!count($list))
{
	return;
}

$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int) $params->get('menuitem');
$mode				= (int) $params->get('mode');

if ($mode == 2)
{
	$baseURL	= 'index.php?option=com_sermonspeaker&view=sermons&catid=';
}
else
{
	$view		= $mode ? 'serie' : 'speaker';
	$baseURL	= 'index.php?option=com_sermonspeaker&view=' . $view . '&id=';
}

require JModuleHelper::getLayoutPath('mod_sermonspeaker', $params->get('layout', 'default'));
