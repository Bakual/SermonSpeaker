<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once __DIR__ . '/helper.php';

// Bail out if user isn't allowed to create a sermon.
$user = JFactory::getUser();

if (!$user->authorise('core.create', 'com_sermonspeaker'))
{
	return;
}

// Prepare Upload Script
JHtml::_('jquery.framework');
JHtml::Script('media/com_sermonspeaker/plupload/plupload.full.min.js');

// Load localisation
$tag  = str_replace('-', '_', JFactory::getLanguage()->getTag());
$path = 'media/com_sermonspeaker/plupload/i18n/';
$file = $tag . '.js';

if (file_exists(JPATH_SITE . '/' . $path . $file))
{
	JHtml::Script($path . $file);
}
else
{
	$tag_array = explode('_', $tag);
	$file      = $tag_array[0] . '.js';

	if (file_exists(JPATH_SITE . '/' . $path . $file))
	{
		JHtml::Script($path . $file);
	}
}

$identifier = 'SermonUpload_' . $module->id . '_';
$c_params   = JComponentHelper::getParams('com_sermonspeaker');

$types = $params->get('types');

if (!$types)
{
	$types = array('audio', 'video', 'addfile');
}

foreach ($types as $type)
{
	ModSermonuploadHelper::loadUploaderScript($identifier, $type, $c_params);
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_sermonupload', $params->get('layout', 'default'));
