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
			$serie		= &$model->getSerie($row->series_id);		// getting the Serie from the Model
			$this->assignRef('serie',$serie);
			$speaker	= &$model->getSpeaker($row->speaker_id);		// getting the Speaker from the Model
			$this->assignRef('speaker',$speaker);
		}

		// Update Statistic
    	$id		= $row->id;
		if ($params->get('track_sermon')) { SermonspeakerController::updateStat('sermons', $id); }
		
		//Check if link targets to an external source
		if (substr($row->sermon_path,0,7) == "http://"){
			$lnk = $row->sermon_path;
		} else {  
			$lnk = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path); 
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
			$serie		= &$model->getSerie($row->series_id);		// getting the Serie from the Model
	    	$breadcrumbs->addItem($serie->series_title, 'index.php?option=com_sermonspeaker&view=serie&id='.$row->series_id.'&Itemid='.$itemid);
		} elseif ($active_view == "speakers") {
			$model		= &$this->getModel();
			$speaker	= &$model->getSpeaker($row->speaker_id);		// getting the Speaker from the Model
	    	$breadcrumbs->addItem($speaker->name, 'index.php?option=com_sermonspeaker&view=speaker&id='.$row->speaker_id.'&Itemid='.$itemid);
		}
    	$breadcrumbs->addItem($row->sermon_title, '');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle().' | '.$row->sermon_title);
		$document->setMetaData("description", strip_tags($row->text));
		$keywords = $this->escape(str_replace(' ', ',', $row->sermon_title).','.str_replace(',', ':', $row->sermon_scripture));
		$document->setMetaData("keywords", $keywords);

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `notes`
		$item->text	= &$row->notes;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		// Trigger Event for `sermon_scripture`
		$item->text	= &$row->sermon_scripture;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

        // push data into the template
		$this->assignRef('row',$row);
		$this->assignRef('lnk',$lnk);
		$this->assignRef('params',$params);			// for Params

		parent::display($tpl);
	}	
}