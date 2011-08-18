<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewMain extends JView
{
	function display( $tpl = null )
	{
		$params	= &JComponentHelper::getParams('com_sermonspeaker');
		if ($params->get('path_mode_video') == ''){
			JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_NOTSAVED'));
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_SERMONSPEAKER_RELOAD'));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER'), 'speakers');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 600, 800);
		}
	}
}