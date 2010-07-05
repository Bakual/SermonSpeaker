<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeries extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' | ' ." ". JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));


		// get Data from Model (/models/series.php)
		$model		=& $this->getModel();
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

		// getting the Speakers for each Series and check if there are avatars at all, only showing column if needed
		$av = NULL;
		foreach ($rows as $row){					
			if (!$av && !empty($row->avatar)){
				$av = 1;
			}
			$speakers	= $model->getSpeakers($row->id);
			$popup = array();
			foreach($speakers as $speaker){
				$popup[] = SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->speaker_id, $speaker->pic, $speaker->name);
			}
			$row->speakers = implode(', ', $popup);
		}
		
		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

        // push data into the template
		$this->assignRef('rows',$rows);
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('av',$av);					// for Avatars
		$this->assignRef('cat',$cat);				// for Category title

		parent::display($tpl);
	}	
}