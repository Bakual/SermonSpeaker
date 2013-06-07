<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$list = modLatestsermonsHelper::getList($params);
if (!count($list))
{
	return;
}
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int)$params->get('ls_mo_menuitem');

require JModuleHelper::getLayoutPath('mod_latestsermons', $params->get('layout', 'default'));
