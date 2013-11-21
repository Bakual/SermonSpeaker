<?php
defined('_JEXEC') or die;
class SermonspeakerViewHelp extends JViewLegacy
{
	function display( $tpl = null )
	{
		SermonspeakerHelper::addSubmenu('help');

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('JHELP'), 'support sermonhelp');
		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}