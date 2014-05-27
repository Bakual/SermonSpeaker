<?php
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sermonspeaker')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}
 
// require helper file
JLoader::register('SermonspeakerHelper', __DIR__ .'/helpers/sermonspeaker.php');

JHtml::_('behavior.tabstate');
JHtml::stylesheet('administrator/components/com_sermonspeaker/sermonspeaker.css');

$controller	= JControllerLegacy::getInstance('Sermonspeaker');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();