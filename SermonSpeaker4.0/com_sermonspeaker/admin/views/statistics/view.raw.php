<?php
defined('_JEXEC') or die;
class SermonspeakerViewStatistics extends JViewLegacy
{
	function display( $tpl = null )
	{
		$document = JFactory::getDocument();
		$document->setMimeEncoding('text/xml');
		// get data from the model
		$this->series	= $this->get('Series');
		$this->speakers	= $this->get('Speakers');
		$this->sermons	= $this->get('Sermons');
		parent::display($tpl);
	}
}