<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewHelp extends JView
{
	function display( $tpl = null )
	{
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('JHELP'), 'sermonhelp');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 600, 900);
		}
	}
}