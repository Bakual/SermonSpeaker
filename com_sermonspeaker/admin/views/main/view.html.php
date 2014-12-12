<?php
defined('_JEXEC') or die;
class SermonspeakerViewMain extends JViewLegacy
{
	function display($tpl = null)
	{
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		if ($params->get('css_icomoon') == '')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NOTSAVED'), 'warning');
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER'), 'speakers');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_sermonspeaker');
		}
	}
}