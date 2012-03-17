<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSitemap extends JView
{
	function display( $tpl = null )
	{
		$document =& JFactory::getDocument();
		$document->setMimeEncoding('text/xml');

		// get data from the model
		$series		=& $this->get('Series');
		$speakers	=& $this->get('Speakers');
		$sermons	=& $this->get('Sermons');

        // push data into the template
		$this->assignRef('series', $series);
		$this->assignRef('speakers', $speakers);
		$this->assignRef('sermons',	$sermons);

		parent::display($tpl);
	}
}