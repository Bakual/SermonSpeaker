<?php
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'modLatestsermonsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);

if (!count($list)) {
	return;
}

$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int)$params->get('ls_mo_menuitem');
$tooltip			= $params->get('ls_show_mouseover');

if ($tooltip) {
	//include only if needed...
	JHTML::_('behavior.tooltip');
}

require JModuleHelper::getLayoutPath('mod_latestsermons', $params->get('layout', '_:default'));