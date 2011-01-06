<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewStatistics extends JView
{
	function display( $tpl = null )
	{
        // get data from the model
		$series		=& $this->get('Series');
		$speakers	=& $this->get('Speakers');
		$sermons	=& $this->get('Sermons');

        // push data into the template
		$this->assignRef('series', $series);
		$this->assignRef('speakers', $speakers);
		$this->assignRef('sermons',	$sermons);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('SermonSpeaker'), 'generic');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 550, 800);
		}
	}
}