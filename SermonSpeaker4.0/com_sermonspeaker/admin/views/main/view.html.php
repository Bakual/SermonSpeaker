<?php
defined('_JEXEC') or die;
class SermonspeakerViewMain extends JViewLegacy
{
	function display( $tpl = null )
	{
		// Switch Layout if in Joomla 3.0
		$layout			= $this->getLayout();
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($layout.'30');
		}

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
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}