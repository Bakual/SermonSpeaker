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
		$document->setMetaData("description",JText::_('SERMFROM')." ".JHTML::date($this->rows[0]->sermon_date, '%B, %Y', 0));
		$document->setMetaData("keywords",JText::_('SERMONLIST'));

		// get Data from Model (/models/archive.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
		$lists		=& $this->get('Order');
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

       // push data into the template
		$this->assignRef('rows',$rows);
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('month',$month);			// for Filtering
		$this->assignRef('year',$year);				// for Filtering
		$this->assignRef('cat',$cat);				// for Category title

		parent::display($tpl);
	}
}