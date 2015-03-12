<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$jinput = JFactory::getApplication()->input;

// Providing backward compatibilty to older SermonSpeaker versions
if ($jinput->get('task') == 'podcast')
{
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . JURI::root() . 'index.php?option=com_sermonspeaker&view=feed&format=raw');

	return;
}

// Joomla doesn't autoload JFile and JFolder
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');
JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');

// Register Helperclasses for autoloading
JLoader::discover('SermonspeakerHelper', JPATH_COMPONENT . '/helpers');
JLoader::discover('SermonspeakerHelperPlayer', JPATH_COMPONENT . '/helpers/player');

// Load languages and merge with fallbacks
$jlang = JFactory::getLanguage();
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, 'en-GB', true);
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, null, true);

$controller	= JControllerLegacy::getInstance('Sermonspeaker');
$controller->execute($jinput->get('task'));
$controller->redirect();
