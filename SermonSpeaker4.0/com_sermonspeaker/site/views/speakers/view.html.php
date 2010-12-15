<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSpeakers extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE').' | '.$document->getTitle());
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));

		// get Data from Model (/models/sermons.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
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
		foreach($rows as $row){
			// Trigger Event for `intro`
			$item->text	= &$row->intro;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
			// Trigger Event for `bio`
			$item->text	= &$row->bio;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		}

        // push data into the template
		$this->assignRef('rows',$rows);             
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('cat',$cat);				// for Category title

		parent::display($tpl);
	}	
}