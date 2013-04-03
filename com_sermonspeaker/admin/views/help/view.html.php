<?php
defined('_JEXEC') or die;
class SermonspeakerViewHelp extends JViewLegacy
{
	function display( $tpl = null )
	{
		SermonspeakerHelper::addSubmenu('help');

		// Switch Layout if in Joomla 3.0
		$layout			= $this->getLayout();
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($layout.'30');
		}

		$this->addToolbar();
		if ($this->joomla30)
		{
			$this->sidebar = JHtmlSidebar::render();
		}

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
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}