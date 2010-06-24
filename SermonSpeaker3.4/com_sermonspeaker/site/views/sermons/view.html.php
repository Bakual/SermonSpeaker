<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSermons extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' | ' ." ". JText::_('SERMONLIST'));
		$document->setMetaData("description",JText::_('SERMONLIST'));
		$document->setMetaData("keywords",JText::_('SERMONLIST'));

		// get Data from Model (/models/sermons.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
		$lists		=& $this->get('Order');
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		$direct_link = $params->get('list_direct_link');
		foreach($rows as $row){
			// Trigger Event for `sermon_scripture`
			$item->text	= &$row->sermon_scripture;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
			if ($direct_link){
				//Check if link targets to an external source
				if (substr($row->sermon_path,0,7) == "http://"){
					$row->link = $row->sermon_path;
				} else {
					$row->link = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
				}
			} else {
				$row->link = JRoute::_("index.php?view=sermon&id=$row->slug");
			}
		}

        // push data into the template
		$this->assignRef('rows',$rows);             
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('cat',$cat);				// for Category title

		parent::display($tpl);
	}	
}