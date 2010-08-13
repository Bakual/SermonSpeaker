<?php 
// no direct access
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JHTML::stylesheet('sermonspeaker.css', 'administrator/components/com_sermonspeaker/');

$controller	= JController::getInstance('Sermonspeaker');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();