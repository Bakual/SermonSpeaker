<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSpeaker extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// get Data from Model (/models/speaker.php)
		$lists		=& $this->get('Order');
        $row		= &$this->get('Data');				// getting the Datarows from the Model

		// Get Data and add Breadcrumbs and Meta according to chosen Template
		$app 			= JFactory::getApplication();
		$breadcrumbs 	= &$app->getPathWay();
		$document 		= &JFactory::getDocument();

		if ($this->getLayout() == "latest-sermons") {
			$sermons	= &$this->get('Sermons');		// getting the Sermons from the Model
		  	if ($params->get('limit_speaker') == 1) {
				$limit = $app->getCfg('list_limit');
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONSOF', $limit, $row->name);
				$bread = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONS', $limit);
			} else {
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_SERMONSOF', $row->name);
				$bread = JText::_('COM_SERMONSPEAKER_SERMONS');
			}
			$breadcrumbs->addItem($row->name.': '.$bread, '');
			$document->setTitle($title.' | '.$document->getTitle());
		} elseif ($this->getLayout() == "popup") {
			$title = $row->name;
		} else {
			$series	= &$this->get('Series');		// getting the Series from the Model
			// check if there are avatars at all, only showing column if needed
			$av = null;
			foreach ($series as $serie){
				if (!empty($serie->avatar)){ // breaking out of foreach if first avatar is found
					$av = 1;
					break;
				}
			}
			$this->assignRef('av',$av);
			$document->setTitle(JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE').' - '.$row->name.' | '.$document->getTitle());
			$breadcrumbs->addItem( $row->name.": ".JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'), '' );
			$title = $row->name;
		}

		// Update Statistic
    	$id		= $row->id;
		if ($params->get('track_speaker')) { SermonspeakerController::updateStat('speakers', $id); }

		// Set Meta
		$document->setMetaData("description",strip_tags($row->intro));
		$document->setMetaData("keywords",$title);

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `intro`
		$item->text	= &$row->intro;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		// Trigger Event for `bio`
		$item->text	= &$row->bio;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

		if ($this->getLayout() == "latest-sermons"){
			$direct_link = $params->get('list_direct_link');
			foreach($sermons as $sermon){
				// Trigger Event for `sermon_scripture`
				$item->text	= &$sermon->sermon_scripture;
				$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
			switch ($direct_link){ // direct links to the file instead to the detailpage
				case '00':
					$sermon->link1 = JRoute::_("index.php?view=sermon&id=$sermon->slug");
					$sermon->link2 = $sermon->link1;
					break;
				case '01':
					$sermon->link1 = JRoute::_("index.php?view=sermon&id=$sermon->slug");
					//Check if link targets to an external source
					if (substr($sermon->sermon_path,0,7) == "http://"){
						$sermon->link2 = $sermon->sermon_path;
					} else {
						$sermon->link2 = SermonspeakerHelperSermonspeaker::makelink($sermon->sermon_path);
					}
					break;
				case '10':
					//Check if link targets to an external source
					if (substr($sermon->sermon_path,0,7) == "http://"){
						$sermon->link1 = $sermon->sermon_path;
					} else {
						$sermon->link1 = SermonspeakerHelperSermonspeaker::makelink($sermon->sermon_path);
					}
					$sermon->link2 = JRoute::_("index.php?view=sermon&id=$sermon->slug");
					break;
				case '11':
					//Check if link targets to an external source
					if (substr($sermon->sermon_path,0,7) == "http://"){
						$sermon->link1 = $sermon->sermon_path;
					} else {
						$sermon->link1 = SermonspeakerHelperSermonspeaker::makelink($sermon->sermon_path);
					}
					$sermon->link2 = $sermon->link1;
					break;
			}
			}
		}

		// push data into the template
		$this->assignRef('row',$row);
		$this->assignRef('title',$title);
		$this->assignRef('lists',$lists);			// for Sorting
		$this->assignRef('series',$series);
		$this->assignRef('sermons',$sermons);
		$this->assignRef('params',$params);		// for Params
		$this->assignRef('document',$document);

		parent::display($tpl);
	}	
}