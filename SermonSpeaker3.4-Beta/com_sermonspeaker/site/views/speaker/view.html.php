<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSpeaker extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// get Data from Model (/models/speaker.php)
        $row		= &$this->get('Data');				// getting the Datarows from the Model

		// Get Data and add Breadcrumbs and Meta according to chosen Template
		$breadcrumbs = &$mainframe->getPathWay();
		$document =& JFactory::getDocument();

		if ($this->getLayout() == "latest-sermons") {
			$sermons	= &$this->get('Sermons');		// getting the Sermons from the Model
			$breadcrumbs->addItem( $row->name.": ".JText::_('LATEST_SERMONS'), '' );
		  	if ($params->get('limit_speaker') == "1") { 
				$title = JText::_('LATEST')." ".$params->get('sermonresults')." ".JText::_('SERMONS_OF')." ".$row->name;
			} else {
				$title = JText::_('SERMONS_OF')." ".$row->name; 
			}
			$document->setTitle($document->getTitle() . ' | ' .JText::_('LATEST_SERMONS').' - '.$row->name);
		} elseif ($this->getLayout() == "popup") {
			
		} else {
			$series	= &$this->get('Series');		// getting the Series from the Model
			$document->setTitle($document->getTitle() . ' | ' .JText::_('SINGLESPEAKER').' - '.$row->name);
			$breadcrumbs->addItem( $row->name.": ".JText::_('SERIES'), '' );
			$title = $row->name; 
		}

		// Update Statistic
    	$id		= $row->id;
		if ($params->get('track_speaker')) { SermonspeakerController::updateStat('speakers', $id); }
		
		// Set Meta
		$document->setMetaData("description",$row->intro);
		$document->setMetaData("keywords",$title);

        // push data into the template
		$this->assignRef('row',$row);             
		$this->assignRef('series',$series);             
		$this->assignRef('sermons',$sermons);             
		$this->assignRef('params',$params);			// for Params

		parent::display($tpl);
	}	
}