<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).'/helper.php');

$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'modSermonarchiveHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);

if (!count($list)) {
	return;
}

$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int)$params->get('menuitem');
$mode				= ($params->get('archive_switch') == 'month');

require JModuleHelper::getLayoutPath('mod_sermonarchive', $params->get('layout', 'default'));