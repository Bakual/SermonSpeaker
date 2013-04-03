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
		$date = getDate();
		if (JRequest::getInt('year') || JRequest::getInt('month')){
			$year = JRequest::getInt('year', $date['year']);
			$month = JRequest::getInt('month', '');
		} else {
			$year = $params->get('year', $date['year']);
			$month = $params->get('month', $date['mon']);
		}

		// get Data from Model (/models/archive.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
		$lists		=& $this->get('Order');
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

		// Create title
		if ($month){
			$date_format = '%B, %Y';
		} else {
			$date_format = '%Y';
		}
		$title = JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE')." ".JHTML::date($rows[0]->sermon_date, $date_format, 0).$cat;

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($title.' | '.$document->getTitle());
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE')." ".JHTML::date($rows[0]->sermon_date, '%B, %Y', 0));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
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
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('title',$title);			// for Title
		$this->assignRef('document',$document);

		parent::display($tpl);
	}
}