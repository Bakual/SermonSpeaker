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
		$user		= JFactory::getUser();
		$groups		= $user->getAuthorisedViewLevels();

		// check if access is not public
		if (!in_array($params->get('access'), $groups)) {
			$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get model data (/models/sermon.php) 
		$state 	= $this->get('State');
		$item 	= $this->get('Item');
		if(!$item){
			$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Check for category ACL
		if ($item->category_access){
			if (!in_array($item->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}
		if ($item->speaker_category_access){
			if (!in_array($item->speaker_category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}
		if ($item->series_category_access){
			if (!in_array($item->series_category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('sermonlayout', 'icon'));
		}
		if ($this->getLayout() == 'icon') {
			JHTML::stylesheet('icon.css', 'media/com_sermonspeaker/css/');
		}

		// Update Statistic
		if ($params->get('track_sermon') && !$user->authorise('com_sermonspeaker.hit', 'com_sermonspeaker')) {
			$model 	= $this->getModel();
			$model->hit();
		}
		
		$this->assignRef('params',		$params);
		$this->assignRef('state', 		$state);
		$this->assignRef('item', 		$item);
		$this->assignRef('user', 		$user);
		$this->assignRef('columns', 	$columns);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();

		// Call Playerhelper anyway, since we assume we either have a download button, popup button or player in any case.
		$this->player = SermonspeakerHelperSermonspeaker::insertPlayer($this->item, $this->item->speaker_name);
		// Add javascript for player if needed
		if (in_array('sermon:player', $this->columns) || JRequest::getCmd('layout', '') == 'popup'){
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
				if($this->player['switch']){
					$this->document->addScriptDeclaration('
						function Video() {
							jwplayer().load(['.$this->player['video'].']).resize("'.$this->params->get('mp_width', '100%').'","'.$this->params->get('mp_height', '400px').'");
							document.getElementById("mediaspace1_wrapper").style.width="'.$this->params->get('mp_width', '100%').'";
							document.getElementById("mediaspace1_wrapper").style.height="'.$this->params->get('mp_height', '400px').'";
						}
					');
					$this->document->addScriptDeclaration('
						function Audio() {
							jwplayer().load(['.$this->player['audio'].']).resize("250","23px");
							document.getElementById("mediaspace1_wrapper").style.width="250px";
							document.getElementById("mediaspace1_wrapper").style.height="23px";
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
	    	$pathway->addItem($this->item->series_title, JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug)));
		} elseif ($menu && ($menu->query['view'] == 'speakers')) {
	    	$pathway->addItem($this->item->speaker_name, JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug)));
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
			$this->document->setMetaData('author', $this->item->speaker_name);
		}
		
		// Add Metadata for Facebook Open Graph API
		$fbadmins	= $this->params->get('fbadmins', '');
		$fbapp_id	= $this->params->get('fbapp_id', '');
		if ($fbadmins || $fbapp_id){
			$this->document->addCustomTag('<meta property="og:title" content="'.$this->item->sermon_title.'"/>');
			$this->document->addCustomTag('<meta property="og:url" content="'.JURI::getInstance()->toString().'"/>');
			$this->document->addCustomTag('<meta property="og:description" content="'.$this->document->getDescription().'"/>');
			if ($this->item->picture){
				$this->document->addCustomTag('<meta property="og:image" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->picture).'"/>');
			} elseif ($this->item->speaker_pic){
				$this->document->addCustomTag('<meta property="og:image" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->speaker_pic).'"/>');
			}
			$this->document->addCustomTag('<meta property="og:site_name" content="'.$app->getCfg('sitename').'"/>');
			$this->document->addCustomTag('<meta property="og:'.$this->player['status'].'" content="'.$this->player['file'].'"/>');
			if($this->player['status'] == 'audio'){
				$this->document->addCustomTag('<meta property="og:type" content="song"/>');
				$this->document->addCustomTag('<meta property="og:audio:title" content="'.$this->item->sermon_title.'"/>');
				if ($this->item->speaker_name){
					$this->document->addCustomTag('<meta property="og:audio:artist" content="'.$this->item->speaker_name.'"/>');
				}
				if ($this->item->series_title){
					$this->document->addCustomTag('<meta property="og:audio:album" content="'.$this->item->series_title.'"/>');
				}
			} else {
				$this->document->addCustomTag('<meta property="og:type" content="movie"/>');
			}
			if ($fbadmins){
				$this->document->addCustomTag('<meta property="fb:admins" content="'.$fbadmins.'"/>');
			}
			if ($fbapp_id){
				$this->document->addCustomTag('<meta property="fb:app_id" content="'.$fbapp_id.'"/>');
			}
		}
	}
}