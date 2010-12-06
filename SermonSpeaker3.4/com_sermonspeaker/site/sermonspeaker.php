<?php
defined('_JEXEC') or die('Restricted access');

if (JRequest::getString('task') == 'podcast') { // providing backward compatibilty to SermonSpeaker3.3.1
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: '.JURI::root().'index.php?option=com_sermonspeaker&view=feed');
	return;
}

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'sermonspeaker.php');

// laden des Joomla! Basis Controllers
require_once (JPATH_COMPONENT.DS.'controller.php');

// Einlesen weiterer Controller falls vorhanden
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Einen eigenen Controller erzeugen
$classname	= 'SermonspeakerController'.$controller;
$controller = new $classname();

// Nachsehen, ob Parameter angekommen sind (Requests)
$controller->execute(JRequest::getCmd('task'));

// Umleitung innerhalb des Controllers
$controller->redirect();