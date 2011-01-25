<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewspeaker extends JView
{
	function display($tpl = null)
	{
		if (!JRequest::getInt('id', 0)){
			JError::raiseWarning(404, JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			return;
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		
		if ($this->getLayout() == 'default') {
			$this->setLayout('series');
		} 

		$model = $this->getModel();
		$model->setState('speaker.layout', $this->getLayout());

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$speaker	= $this->get('Speaker');
		$pagination	= $this->get('Pagination');

		// add breadcrumbs and define page title according to chosen layout
		$breadcrumbs = $app->getPathWay();
		if ($this->getLayout() == 'latest-sermons') {
		  	if ($params->get('limit_speaker') == 1) {
				$limit = $app->getCfg('list_limit');
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONSOF', $limit, $speaker->name);
				$bread = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONS', $limit);
			} else {
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_SERMONSOF', $speaker->name);
				$bread = JText::_('COM_SERMONSPEAKER_SERMONS');
			}
			// Add Breadcrumbs
			$breadcrumbs->addItem($speaker->name.': '.$bread, '');
		} elseif ($this->getLayout() == 'series') {
			// check if there are avatars at all, only showing column if needed
			$av = 0;
			foreach ($items as $item){
				if (!empty($item->avatar)){ // breaking out of foreach if first avatar is found
					$av = 1;
					break;
				}
			}
			$this->assignRef('av', $av);
			$title = JText::sprintf('JPAGETITLE', $speaker->name, JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'));
			// Add Breadcrumbs
			$breadcrumbs->addItem($speaker->name.': '.JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'), '');
		} else {
			$title = $speaker->name;
		}

		// Update Statistic
		if ($params->get('track_speaker')) {
			$model = $this->getModel();
			$model->hit();
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `intro`
		$item->text	= &$speaker->intro;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		// Trigger Event for `bio`
		$item->text	= &$speaker->bio;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

		if ($this->getLayout() == "latest-sermons"){
			$direct_link = $params->get('list_direct_link', '00');
			foreach($items as $row){
				// Trigger Event for `sermon_scripture`
				$item->text	= &$row->sermon_scripture;
				$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
				switch ($direct_link){ // direct links to the file instead to the detailpage
					case '00':
						$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						$row->link2 = $row->link1;
						break;
					case '01':
						$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						$row->link2 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
						break;
					case '10':
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
						$row->link2 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						break;
					case '11':
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
						$row->link2 = $row->link1;
						break;
				}
			}
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('speaker',		$speaker);
		$this->assignRef('title',		$title);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();

		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, $this->title);
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description = $description.' ';
		}
		$this->document->setMetaData('description', $description.strip_tags($this->speaker->intro));

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.$this->title);
	}
}