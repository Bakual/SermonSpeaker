<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sermonspeaker')) 
{
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
 
// require helper file
JLoader::register('SermonspeakerHelper', dirname(__FILE__).DS.'helpers'.DS.'sermonspeaker.php');

// import joomla controller library
jimport('joomla.application.component.controller');

JHTML::stylesheet('administrator/components/com_sermonspeaker/sermonspeaker.css');

// Load languages and merge with fallbacks
$jlang = JFactory::getLanguage();
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, 'en-GB', true);
$jlang->load('com_sermonspeaker', JPATH_COMPONENT, null, true);

$controller	= JController::getInstance('Sermonspeaker');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();