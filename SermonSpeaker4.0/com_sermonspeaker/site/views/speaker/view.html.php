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
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		
		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

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

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('columns', 	$columns);
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

		// Add javascript for player if needed
		if (in_array('speaker:player', $this->columns) || $this->getLayout() == 'latest-sermons'){
			JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js', true);
			$this->player = SermonspeakerHelperSermonspeaker::insertPlayer($this->items);
			if($this->params->get('fileswitch')){
				$this->document->addScriptDeclaration('
					function Video() {
						jwplayer().load(['.$this->player['video-pl'].']);
					}
				');
				$this->document->addScriptDeclaration('
					function Audio() {
						jwplayer().load(['.$this->player['audio-pl'].']);
					}
				');
			}
		}
		
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