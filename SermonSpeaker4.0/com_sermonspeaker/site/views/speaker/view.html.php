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
		$app		= JFactory::getApplication();
		if (!JRequest::getInt('id', 0)){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		$params		= $app->getParams();
		$user		= JFactory::getUser();
		
		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}
		$col_speaker = $params->get('col_speaker');
		if (!$col_speaker){
			$col_speaker = array();
		}

		if ($this->getLayout() == 'default') {
			$this->setLayout('series');
		} 

		$model = $this->getModel();
		$model->setState('speaker.layout', $this->getLayout());

		// Get some data from the models
		$state		= $this->get('State');
		$speaker	= $this->get('Speaker');
		if(!$speaker){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// check if access is not public
		if ($speaker->category_access){
			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($speaker->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Get more data from the models
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		if ($this->getLayout() == 'series') {
			// check if there are avatars at all, only showing column if needed
			$av = 0;
			foreach ($items as $item){
				if (!empty($item->avatar)){ // breaking out of foreach if first avatar is found
					$av = 1;
					break;
				}
			}
			$this->assignRef('av', $av);
			$col_serie = $params->get('col_serie');
			if (!$col_serie){
				$col_serie = array();
			}
			$this->assignRef('col_serie', $col_serie);
		}

		// Update Statistic
		if ($params->get('track_speaker')) {
			$user	= JFactory::getUser();
			if (!$user->authorise('com_sermonspeaker.hit', 'com_sermonspeaker')){
				$model 	= $this->getModel();
				$model->hit();
			}
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
		$this->assignRef('col_speaker', $col_speaker);
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
		if (in_array('speaker:player', $this->columns) && ($this->getLayout() == 'latest-sermons') && count($this->items)){
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
		
		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'));
		}

		// Set Pagetitle
		if ($this->speaker->name && (!$menu || $menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'speaker' || $menu->query['id'] != $this->item->id)){
			$title = $this->speaker->name;
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->speaker->name);

		// Set MetaData
		if ($this->speaker->metadesc){
			$this->document->setDescription($this->speaker->metadesc);
		} elseif ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->speaker->metakey){
			$this->document->setMetadata('keywords', $this->speaker->metakey);
		} elseif ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($app->getCfg('MetaAuthor')){
			$this->document->setMetaData('author', $this->speaker->name);
		}
	}
}