<?php
defined('_JEXEC') or die;
class SermonspeakerViewLanguages extends JViewLegacy
{
	function display( $tpl = null )
	{
		SermonspeakerHelper::addSubmenu('languages');

		// Switch Layout if in Joomla 3.0
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($this->getLayout().'30');
		}

		$url				= 'http://www.sermonspeaker.net/languages.raw';
		$this->xml			= simplexml_load_file($url);
		$this->languages	= JFactory::getLanguage()->getKnownLanguages();

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
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'), 'languages');
		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}