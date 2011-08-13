<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerie extends JView
{
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		if (!JRequest::getInt('id', 0)){
			$app->redirect(JRoute::_('index.php?view=series'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		// Initialise variables.
		$params		= $app->getParams();
		$user		= JFactory::getUser();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}
		$col_serie = $params->get('col_serie');
		if (!$col_serie){
			$col_serie = array();
		}

		// Get some data from the models
		$state		= $this->get('State');
		$serie		= $this->get('Serie');
		if(!$serie){
			$app->redirect(JRoute::_('index.php?view=series'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// check if access is not public
		if ($serie->category_access){
			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($serie->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=series'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Get more data from the models
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Update Statistic
		if ($params->get('track_series')) {
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
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('col_serie', 	$col_serie);
		$this->assignRef('serie',		$serie);
		$this->assignRef('cat',			$cat);

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
		if (in_array('serie:player', $this->columns) && count($this->items)){
			require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');
			$this->player = new SermonspeakerHelperPlayer($this->params);
			$this->player->prepare($this->items);
			if ($this->player->player == 'PixelOut'){
				JHTML::Script('media/com_sermonspeaker/player/audio_player/audio-player.js');
				$this->document->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'media/com_sermonspeaker/player/audio_player/player.swf", {
					width: "100%",
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			} elseif ($this->player->player == 'JWPlayer'){
				JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js');
				if($this->player->toggle){
					$this->document->addScriptDeclaration('
						function Video() {
							jwplayer().load(['.$this->player->playlist['video'].']).resize("'.$this->params->get('mp_width', '100%').'","'.$this->params->get('mp_height', '400px').'");
							document.getElementById("mediaspace1_wrapper").style.width="'.$this->params->get('mp_width', '100%').'";
							document.getElementById("mediaspace1_wrapper").style.height="'.$this->params->get('mp_height', '400px').'";
						}
					');
					$this->document->addScriptDeclaration('
						function Audio() {
							jwplayer().load(['.$this->player->playlist['audio'].']).resize("100%","80px");
							document.getElementById("mediaspace1_wrapper").style.width="100%";
							document.getElementById("mediaspace1_wrapper").style.height="80px";
						}
					');
				}
			}
		}
		
		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
		}

		// Set Pagetitle
		if ($this->serie->series_title && (!$menu || $menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'serie' || $menu->query['id'] != $this->item->id)){
			$title = $this->serie->series_title;
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->serie->series_title);

		// Set MetaData
		if ($this->serie->metadesc){
			$this->document->setDescription($this->serie->metadesc);
		} elseif ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->serie->metakey){
			$this->document->setMetadata('keywords', $this->serie->metakey);
		} elseif ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($app->getCfg('MetaTitle')){
			$this->document->setMetaData('title', $this->serie->series_title);
		}
	}
}