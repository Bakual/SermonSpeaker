<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$cacheparams = new stdClass;
$cacheparams->cachemode = 'safeuri';
$cacheparams->class = 'modRelatedSermonsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = array('id'=>'int','Itemid'=>'int');
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);

if (!count($list)) {
	return;
}


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$showDate 		 = $params->get('showDate', 0);
require JModuleHelper::getLayoutPath('mod_related_sermons', $params->get('layout', 'default'));
