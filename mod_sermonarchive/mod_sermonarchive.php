<?php
// no direct access
defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$list = modSermonarchiveHelper::getList($params);
if (!count($list))
{
	return;
}
$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int)$params->get('menuitem');
$mode				= ($params->get('archive_switch') == 'month');

require JModuleHelper::getLayoutPath('mod_sermonarchive', $params->get('layout', 'default'));