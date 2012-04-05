<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewStatistics extends JView
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