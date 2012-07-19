<?php
defined('_JEXEC') or die;

class SermonspeakerController extends JControllerLegacy 
{
	public function display($cachable = false, $urlparams = false)
	{
		// Set a default view 
		if (!JRequest::getWord('view')) {
			JRequest::setVar('view', 'main');
		}
		require_once JPATH_COMPONENT.'/helpers/sermonspeaker.php';

		parent::display();

		// Load the submenu.
		$view = JRequest::getWord('view', 'main');
		if ($view != 'main'){
			SermonspeakerHelper::addSubmenu($view);
		}

		return $this;
	}
}