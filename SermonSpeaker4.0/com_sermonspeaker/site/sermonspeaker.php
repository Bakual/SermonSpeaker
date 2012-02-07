<?php
defined('_JEXEC') or die('Restricted access');

// providing backward compatibilty to older SermonSpeaker versions
// Bug: Doesn't take into account additional filters (type, cat)
if ((JRequest::getCmd('view') == 'feed' && (JRequest::getCmd('format') != 'raw')) || (JRequest::getCmd('task') == 'podcast')) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.JURI::root().'index.php?option=com_sermonspeaker&view=feed&format=raw');
	return;
}

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'sermonspeaker.php');

$controller	= JController::getInstance('Sermonspeaker', array('default_view' => 'sermons'));
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();