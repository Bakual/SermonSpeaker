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
		global $mainframe, $option;
		
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' | ' ." ". JText::_('SERIESMAIN'));
		$document->setMetaData("description",JText::_('SERIESMAIN'));
		$document->setMetaData("keywords",JText::_('SERIESMAIN'));


		// get Data from Model (/models/series.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model
        $av			=& $this->get('Avatar');		// Let the Model check how many Avatarpictures there are

        // push data into the template
		$this->assignRef('rows',$rows);             
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('av',$av);					// for Avatars

		parent::display($tpl);
	}	
}