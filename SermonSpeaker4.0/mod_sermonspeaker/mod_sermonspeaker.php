<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$cacheparams = new stdClass;
$cacheparams->cachemode = 'static';
$cacheparams->class = 'modSermonspeakerHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);

if (!count($list)) {
	return;
}

$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$itemid				= (int)$params->get('menuitem');
$mode				= (int)$params->get('mode');
if ($mode == 2){
	$baseURL	= 'index.php?option=com_sermonspeaker&view=sermons&sermon_cat=';
} else {
	$view		= $mode ? 'serie' : 'speaker';
	$baseURL	= 'index.php?option=com_sermonspeaker&view='.$view.'&id=';
}

require JModuleHelper::getLayoutPath('mod_sermonspeaker', $params->get('layout', 'default'));