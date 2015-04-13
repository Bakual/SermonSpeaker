<?php
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sermonspeaker'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Joomla doesn't autoload JFile and JFolder
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');
JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');

// Register Helperclass for autoloading
JLoader::register('SermonspeakerHelper', JPATH_COMPONENT . '/helpers/sermonspeaker.php');

// Import Plugins
JPluginHelper::importPlugin('sermonspeaker');

JHtml::_('behavior.tabstate');
JHtml::stylesheet('administrator/components/com_sermonspeaker/sermonspeaker.css');

$controller = JControllerLegacy::getInstance('Sermonspeaker');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
