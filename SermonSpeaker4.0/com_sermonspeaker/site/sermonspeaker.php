<?php
defined('_JEXEC') or die;

$task = JFactory::getApplication()->input->get('task');

// providing backward compatibilty to older SermonSpeaker versions
if ($task == 'podcast') {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.JURI::root().'index.php?option=com_sermonspeaker&view=feed&format=raw');
	return;
}

require_once(JPATH_COMPONENT.'/helpers/route.php');
require_once(JPATH_COMPONENT.'/helpers/sermonspeaker.php');

// Load languages and merge with fallbacks
$jlang = JFactory::getLanguage();
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, 'en-GB', true);
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, null, true);

$controller	= JControllerLegacy::getInstance('Sermonspeaker', array('default_view' => 'sermons'));
$controller->execute($task);
$controller->redirect();