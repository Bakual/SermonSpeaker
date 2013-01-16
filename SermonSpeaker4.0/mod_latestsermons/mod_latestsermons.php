<?php
defined('_JEXEC') or die;
require_once (dirname(__FILE__).'/helper.php');
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
require JModuleHelper::getLayoutPath('mod_latestsermons', $params->get('layout', '_:default'));
