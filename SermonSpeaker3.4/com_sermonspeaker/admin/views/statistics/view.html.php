<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewStatistics extends JView
{
	function display( $tpl = null )
	{
		global $option;

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