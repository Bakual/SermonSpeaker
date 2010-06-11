<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSermon extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		
		// get Data from Model (/models/sermon.php)
        $row = &$this->get('Data');			// getting the Datarows from the Model
		if ($this->getLayout() == "default") {
			if ($params->get('sermonlayout') == 1) { $this->setLayout('allinrow'); }
			elseif ($params->get('sermonlayout') == 2) { $this->setLayout('newline'); }
			elseif ($params->get('sermonlayout') == 3) { $this->setLayout('extnewline'); }
		} 
		if ($this->getLayout() == "extnewline") {
			$model		= &$this->getModel();
			$serie		= &$model->getSerie($row[0]->series_id);		// getting the Serie from the Model
			$this->assignRef('serie',$serie);
			$speaker	= &$model->getSpeaker($row[0]->speaker_id);		// getting the Speaker from the Model
			$this->assignRef('speaker',$speaker);
		}

		// Update Statistic
    	$id		= $row[0]->id;
		if ($params->get('track_sermon')) { SermonspeakerController::updateStat('sermons', $id); }
		
		//Check if link targets to an external source
		if (substr($row[0]->sermon_path,0,7) == "http://"){
			$lnk = $row[0]->sermon_path;
		} else {  
			$lnk = SermonspeakerHelperSermonspeaker::makelink($row[0]->sermon_path); 
		}
		
		// get active View from Menuitem
		$menu = &JSite::getMenu();
		$active = $menu->getActive();
		$active_view = $active->query[view];
		$itemid = $active->id;

		// add Breadcrumbs
		$app 			= JFactory::getApplication();
		$breadcrumbs	= &$app->getPathWay();
		if ($active_view == "series") {
			$model		= &$this->getModel();
			$serie		= &$model->getSerie($row[0]->series_id);		// getting the Serie from the Model
	    	$breadcrumbs->addItem($serie->series_title, 'index.php?option=com_sermonspeaker&view=serie&id='.$row[0]->series_id.'&Itemid='.$itemid);
		} elseif ($active_view == "speakers") {
			$model		= &$this->getModel();
			$speaker	= &$model->getSpeaker($row[0]->speaker_id);		// getting the Speaker from the Model
	    	$breadcrumbs->addItem($speaker->name, 'index.php?option=com_sermonspeaker&view=speaker&id='.$row[0]->speaker_id.'&Itemid='.$itemid);
		}
    	$breadcrumbs->addItem($row[0]->sermon_title, '');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle().' | '.$row[0]->sermon_title);
		$document->setMetaData("description", strip_tags($row[0]->notes));
		$keywords = $this->escape(str_replace(' ', ',', $row[0]->sermon_title).','.str_replace(',', ':', $row[0]->sermon_scripture));
		$document->setMetaData("keywords", $keywords);

        // push data into the template
		$this->assignRef('row',$row);             
		$this->assignRef('lnk',$lnk);             
		$this->assignRef('params',$params);			// for Params

		parent::display($tpl);
	}	
}