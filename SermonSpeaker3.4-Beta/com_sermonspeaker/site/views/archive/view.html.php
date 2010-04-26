<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewArchive extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Getting year/month from Request, if not present take from Parameter, if not present take actual year/month
		$params = &JComponentHelper::getParams('com_sermonspeaker');
		$date=getDate();
		$pmonth	= $params->get('month',$date[mon]);
		$pyear	= $params->get('year',$date[year]);
		$month	= JRequest::getInt('month', $pmonth);
		$year	= JRequest::getInt('year', $pyear);

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' | ' ." ". JText::_('SERMONLIST'));
//		if ($desc) {$document->setMetaData("description",$desc);}
//		if ($tags) {$document->setMetaData("keywords",$tags);}

		// get Data from Model (/models/archive.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

        // push data into the template
		$this->assignRef('rows',$rows);
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('month',$month);			// for Sorting
		$this->assignRef('year',$year);				// for Sorting

		parent::display($tpl);
	}
}