<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewMain extends JView
{
	function display( $tpl = null )
	{
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		if ($params->get('count_items_type') == ''){
			JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_NOTSAVED'));
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