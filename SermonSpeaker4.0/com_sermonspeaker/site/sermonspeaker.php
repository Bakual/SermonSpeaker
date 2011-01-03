<?php
defined('_JEXEC') or die('Restricted access');

if (JRequest::getString('task') == 'podcast') { // providing backward compatibilty to SermonSpeaker3.3.1
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.JURI::root().'index.php?option=com_sermonspeaker&view=feed&format=raw');
	return;
}

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/route.php';
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'sermonspeaker.php');

$controller	= JController::getInstance('Sermonspeaker');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();