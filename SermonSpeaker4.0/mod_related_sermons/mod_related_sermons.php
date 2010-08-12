<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$list = modRelatedSermonsHelper::getList($params);

if (!count($list)) {
	return;
}

$showDate = $params->get('showDate', 0);
require(JModuleHelper::getLayoutPath('mod_related_sermons'));
