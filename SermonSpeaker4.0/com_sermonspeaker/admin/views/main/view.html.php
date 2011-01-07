<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewMain extends JView
{
	function display( $tpl = null )
	{
		$notsaved = NULL;
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		if ($params->get('alt_player') == ''){
			$notsaved = '<thead>';
			$notsaved .= '<tr><td bgcolor="salmon"><center><strong>'.JText::_('COM_SERMONSPEAKER_NOTSAVED').'</strong></center></td></tr>';
			$notsaved .= '</thead>';
		}
		$this->assignRef('notsaved', $notsaved);

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
			JToolBarHelper::preferences('com_sermonspeaker', 550, 800);
		}
	}
}