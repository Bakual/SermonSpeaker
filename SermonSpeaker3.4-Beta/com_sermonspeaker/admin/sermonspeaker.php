<?php 
defined('_JEXEC') or die('Restricted access'); 

// Require the base controller
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

JHTML::stylesheet('sermonspeaker.css', 'administrator/components/com_sermonspeaker/');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname	= 'SermonspeakerController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();