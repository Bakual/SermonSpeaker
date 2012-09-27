<?php
defined('_JEXEC') or die;
class SermonspeakerViewTools extends JViewLegacy
{
	function display( $tpl = null )
	{
		$layout = $this->getLayout();
		if ($layout !== 'time')
		{
			SermonspeakerHelper::addSubmenu('tools');
		}

		// Switch Layout if in Joomla 3.0
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($layout.'30');
		}

		// Check if PreachIt is installed
		$app = JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= "SHOW TABLES";
		$db->setQuery($query);
		$tables	= $db->loadColumn();
		$prefix	= $app->getCfg('dbprefix');
		$this->pi	= (in_array($prefix.'pistudies', $tables)) ? true : false;

		// We don't need toolbar in the modal window.
		if ($layout !== 'time') {
			$this->addToolbar();
			if ($this->joomla30)
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'), 'tools');
		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}