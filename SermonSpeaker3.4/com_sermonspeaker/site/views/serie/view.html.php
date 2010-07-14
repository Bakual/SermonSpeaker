<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerie extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$document =& JFactory::getDocument();

		// Add swfobject-javascript for player
		$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
		
		// get Data from Model (/models/serie.php)
        $rows		= &$this->get('Data');			// getting the Datarows from the Model
		$lists		=& $this->get('Order');
        $pagination	= &$this->get('Pagination');	// getting the JPaginationobject from the Model
		$serie		= &$this->get('Serie');			// getting the Serie from the Model

		// Update Statistic
    	$id		= $serie->id;
		if ($params->get('track_series')) { SermonspeakerController::updateStat('series', $id); }
		
		// get active View from Menuitem
		$menu = &JSite::getMenu();
		$active = $menu->getActive();
		$active_view = $active->query[view];
		$itemid = $active->id;
		if ($active_view == "speakers" || $active_view == "sermons") {
			$menuitems = $menu->getItems('link', 'index.php?option=com_sermonspeaker&view=series');
			if ($menuitems) {
				$itemid = $menuitems[0]->id;
				$menu->setActive($itemid); // set active menu to Series View if a menu for this exists, otherwise leave it as it is for now
			}
		}
		
		// add Breadcrumbs
		$app 			= JFactory::getApplication();
		$breadcrumbs	= &$app->getPathWay();
		$breadcrumbs->addItem($serie->series_title);

		// Set Meta
		$document->setTitle(JText::_('COM_SERMONSPEAKER_SERIE_TITLE').": ".$serie->series_title.' | '.$document->getTitle());
		$document->setMetaData("description",strip_tags($serie->series_description));
		$document->setMetaData("keywords",$serie->series_title);

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `series_description`
		$item->text	= &$serie->series_description;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		$direct_link = $params->get('list_direct_link');
		foreach($rows as $row){
			// Trigger Event for `sermon_scripture`
			$item->text	= &$row->sermon_scripture;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
			switch ($direct_link){ // direct links to the file instead to the detailpage
				case '00':
					$row->link1 = JRoute::_("index.php?view=sermon&id=$row->slug");
					$row->link2 = $row->link1;
					break;
				case '01':
					$row->link1 = JRoute::_("index.php?view=sermon&id=$row->slug");
					//Check if link targets to an external source
					if (substr($row->sermon_path,0,7) == "http://"){
						$row->link2 = $row->sermon_path;
					} else {
						$row->link2 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
					}
					break;
				case '10':
					//Check if link targets to an external source
					if (substr($row->sermon_path,0,7) == "http://"){
						$row->link1 = $row->sermon_path;
					} else {
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
					}
					$row->link2 = JRoute::_("index.php?view=sermon&id=$row->slug");
					break;
				case '11':
					//Check if link targets to an external source
					if (substr($row->sermon_path,0,7) == "http://"){
						$row->link1 = $row->sermon_path;
					} else {
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
					}
					$row->link2 = $row->link1;
					break;
			}
		}

        // push data into the template
		$this->assignRef('rows',$rows);             
		$this->assignRef('serie',$serie);             
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('itemid',$itemid);

		parent::display($tpl);
	}	
}