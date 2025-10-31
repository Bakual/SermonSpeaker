<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

// Bail out if user isn't allowed to create a sermon.
$user = Factory::getUser();

if (!$user->authorise('core.create', 'com_sermonspeaker'))
{
	return;
}

// Prepare Upload Script
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'com_sermonspeaker/plupload/plupload.full.min.js', array('relative' => true));

// Load localisation
$tag  = str_replace('-', '_', Factory::getLanguage()->getTag());
$path = 'com_sermonspeaker/plupload/i18n/';
$file = $tag . '.js';

if (!HTMLHelper::_('script', $path . $file, array('relative' => true, 'pathOnly' => true)))
{
	$tag_array = explode('_', $tag);
	$file      = $tag_array[0] . '.js';

}

HTMLHelper::_('script', $path . $file, array('relative' => true));

$identifier = 'SermonUpload_' . $module->id . '_';
$c_params   = ComponentHelper::getParams('com_sermonspeaker');

$types = $params->get('types');

if (!$types)
{
	$types = array('audio', 'video', 'addfile');
}

foreach ($types as $type)
{
	ModSermonuploadHelper::loadUploaderScript($identifier, $type, $c_params);
}

require ModuleHelper::getLayoutPath('mod_sermonupload', $params->get('layout', 'default'));
