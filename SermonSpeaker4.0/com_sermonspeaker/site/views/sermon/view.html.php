<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
	
/**
 * HTML Sermon View class for the Sermonspeaker component
 *
 * @package		Sermonspeaker
 */
class SermonspeakerViewSermon extends JView
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		if (!JRequest::getInt('id', 0)){
			JError::raiseWarning(404, JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			return;
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get model data (/models/sermon.php) 
		$state 	= $this->get('State');
		$item 	= $this->get('Item');

		// check if access is not public
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		// Set ROOT category to public
		if ($item->category_access === NULL){
			$item->category_access = 1;
		}

		if ((!in_array($params->get('access'), $groups)) || (!in_array($item->category_access, $groups))) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		$model = &$this->getModel();
		if ($item->speaker_id) {
			$speaker = &$model->getSpeaker($item->speaker_id);	// getting the Speaker from the Model
		} else {
			$speaker->name = '';
			$speaker->id = 0;
			$speaker->pic = '';
		}
		if ($item->series_id) {
			$serie	= &$model->getSerie($item->series_id);		// getting the Serie from the Model
			$this->assignRef('serie', $serie);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('sermonlayout', 'icon'));
		} 

		// Update Statistic
		if ($params->get('track_series')) {
			$model = $this->getModel();
			$model->hit();
		}
		$this->assignRef('params',		$params);
		$this->assignRef('state', 		$state);
		$this->assignRef('item', 		$item);
		$this->assignRef('user', 		$user);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('speaker',		$speaker);

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
		if (in_array('sermon:player', $this->columns) || JRequest::getCmd('layout', '') == 'popup'){
			$this->player = SermonspeakerHelperSermonspeaker::insertPlayer($this->item, $this->speaker->name);
			if ($this->player['player'] == 'PixelOut'){
				JHTML::Script('media/com_sermonspeaker/player/audio_player/audio-player.js');
				$this->document->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'media/com_sermonspeaker/player/audio_player/player.swf", {
					width: 290,
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			} elseif ($this->player['player'] == 'JWPlayer'){
				JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js');
			}
		}
		
		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERMON_TITLE'));
		}

		// Set Pagetitle
		if ($this->item->sermon_title && (!$menu || $menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'sermon' || $menu->query['id'] != $this->item->id)){
			$title = $this->item->sermon_title;
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		if ($menu && ($menu->query['view'] == 'series')) {
	    	$pathway->addItem($this->serie->series_title, JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->serie->slug)));
		} elseif ($menu && ($menu->query['view'] == 'speakers')) {
	    	$pathway->addItem($this->speaker->name, JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->speaker->slug)));
		}
    	$pathway->addItem($this->item->sermon_title, '');

		// Set MetaData
		if ($this->item->metadesc){
			$this->document->setDescription($this->item->metadesc);
		} elseif ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->item->metakey){
			$this->document->setMetadata('keywords', $this->item->metakey);
		} elseif ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($app->getCfg('MetaTitle')){
			$this->document->setMetaData('title', $this->item->sermon_title);
		}
		if ($app->getCfg('MetaAuthor')){
			$this->document->setMetaData('author', $this->speaker->name);
		}
	}
}