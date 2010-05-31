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
		global $option;
		
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// get Data from Model (/models/serie.php)
        $rows		= &$this->get('Data');			// getting the Datarows from the Model
		$lists		=& $this->get('Order');
        $pagination	= &$this->get('Pagination');	// getting the JPaginationobject from the Model
		$serie		= &$this->get('Serie');			// getting the Serie from the Model

		// Update Statistic
    	$id		= $serie[0]->id;
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
		$breadcrumbs->addItem($serie[0]->series_title);

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle().' | '.JText::_('SINGLESERIES').": ".$serie[0]->series_title);
		$document->setMetaData("description",strip_tags($serie[0]->series_description));
		$document->setMetaData("keywords",$serie[0]->series_title);

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