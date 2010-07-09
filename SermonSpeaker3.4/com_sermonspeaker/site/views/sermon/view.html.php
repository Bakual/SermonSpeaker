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

		$app 	=& JFactory::getApplication();
		$user	=& JFactory::getUser();
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$document =& JFactory::getDocument();

		// Add swfobject-javascript for player
		$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
		
		// Check to see if the user has access to view the sermon
		$aid	= $user->get('aid'); // 0 = public, 1 = registered, 2 = special

		if ($params->get('access') > $aid){
			if (!$aid){
				// Redirect to login
				$uri	= JFactory::getURI();
				$return	= $uri->toString();

				$url  = 'index.php?option=com_user&view=login&return='.base64_encode($return);

				//$url	= JRoute::_($url, false);
				$app->redirect($url, JText::_('You must login first'));
			} else {
				JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
				return;
			}
		}

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
		$document->setTitle($document->getTitle().' | '.$row->sermon_title);
		$document->setMetaData("description", strip_tags($row->notes));
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
		// enable this lines if you want to parse the custom 1 and 2 fields by content plugins
		// Trigger Event for `custom1`
//		$item->text	= &$row->custom1;
//		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		// Trigger Event for `custom2`
//		$item->text	= &$row->custom2;
//		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

        // push data into the template
		$this->assignRef('row',$row);
		$this->assignRef('lnk',$lnk);
		$this->assignRef('params',$params);			// for Params

		parent::display($tpl);
	}	
}