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
		$lists		=& $this->get('Order');
        $row		= &$this->get('Data');				// getting the Datarows from the Model

		// Get Data and add Breadcrumbs and Meta according to chosen Template
		$breadcrumbs = &$mainframe->getPathWay();
		$document =& JFactory::getDocument();

		if ($this->getLayout() == "latest-sermons") {
			$sermons	= &$this->get('Sermons');		// getting the Sermons from the Model
		  	if ($params->get('limit_speaker') == 1) {
				$title = JText::_('LATEST')." ".$params->get('sermonresults')." ".JText::_('SERMONS_OF')." ".$row->name;
				$bread = JText::_('LATEST')." ".$params->get('sermonresults')." ".JText::_('SERMONS');
			} else {
				$title = JText::_('SERMONS_OF')." ".$row->name;
				$bread = JText::_('SERMONS');
			}
			$breadcrumbs->addItem($row->name.': '.$bread, '');
			$document->setTitle($document->getTitle().' | '.$title);
		} elseif ($this->getLayout() == "popup") {
			$title = $row->name;
		} else {
			$series	= &$this->get('Series');		// getting the Series from the Model
			// check if there are avatars at all, only showing column if needed
			$av = null;
			foreach ($series as $serie){
				if (!empty($serie->avatar)){ // breaking out of foreach if first avatar is found
					$av = 1;
					break;
				}
			}
			$this->assignRef('av',$av);
			$document->setTitle($document->getTitle() . ' | ' .JText::_('SINGLESPEAKER').' - '.$row->name);
			$breadcrumbs->addItem( $row->name.": ".JText::_('SINGLESPEAKER'), '' );
			$title = $row->name;
		}

		// Update Statistic
    	$id		= $row->id;
		if ($params->get('track_speaker')) { SermonspeakerController::updateStat('speakers', $id); }

		// Set Meta
		$document->setMetaData("description",strip_tags($row->intro));
		$document->setMetaData("keywords",$title);

        // push data into the template
		$this->assignRef('row',$row);
		$this->assignRef('title',$title);
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('series',$series);
		$this->assignRef('sermons',$sermons);
		$this->assignRef('params',$params);		// for Params

		parent::display($tpl);
	}	
}