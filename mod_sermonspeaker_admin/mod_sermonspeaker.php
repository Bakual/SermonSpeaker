<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Administrator.Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

// Include the helper functions only once.
require_once __DIR__ . '/helper.php';

// Get module data.
$list  = array();
$types = $params->get('types');

if (!$types)
{
	$types = array('sermons', 'series', 'speakers');
}

foreach ($types as $type)
{
	$items = ModSermonspeakerHelper::getList($params, $type);

	if ($items)
	{
		$list[$type] = $items;
	}
}

// Render the module
require JModuleHelper::getLayoutPath('mod_sermonspeaker', $params->get('layout', 'default'));
