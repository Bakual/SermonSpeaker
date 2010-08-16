<?php 
// no direct access
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JHTML::stylesheet('administrator/components/com_sermonspeaker/sermonspeaker.css');

$controller	= JController::getInstance('Sermonspeaker');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();