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

	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		if (!JRequest::getInt('id', 0)){
			JError::raiseWarning(404, JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			return;
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

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

		// Set layout from parameters if not already set elsewhere
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
	    	$pathway->addItem($this->item->name, JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug)));
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
			$this->document->setMetaData('author', $this->item->name);
		}
		
		// Add Metadata for Facebook Open Graph API
		$fbadmins	= $this->params->get('fbadmins', '');
		$fbapp_id	= $this->params->get('fbapp_id', '');
		if ($fbadmins || $fbapp_id){
			$this->document->addCustomTag('<meta property="og:title" content="'.$this->item->sermon_title.'"/>');
			$this->document->addCustomTag('<meta property="og:url" content="'.JURI::getInstance()->toString().'"/>');
			$this->document->addCustomTag('<meta property="og:description" content="'.$this->document->getDescription().'"/>');
			$this->document->addCustomTag('<meta property="og:site_name" content="'.$app->getCfg('sitename').'"/>');
			$this->document->addCustomTag('<meta property="og:type" content="sermon"/>');
			if ($this->item->picture){
				$this->document->addCustomTag('<meta property="og:image" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->picture).'"/>');
			} elseif ($this->item->pic){
				$this->document->addCustomTag('<meta property="og:image" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->pic).'"/>');
			}
			if($this->item->videofile){
				if((strpos($this->item->videofile, 'http://vimeo.com') === 0) || (strpos($this->item->videofile, 'http://player.vimeo.com') === 0)){
					$id			= trim(strrchr($this->item->videofile, '/'), '/ ');
					$file	= 'http://vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
					$this->document->addCustomTag('<meta property="og:video" content="'.$file.'"/>');
				} else {
					$this->document->addCustomTag('<meta property="og:video" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->videofile).'"/>');
				}
			}
			if($this->item->audiofile){
				$this->document->addCustomTag('<meta property="og:audio" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->audiofile).'"/>');
				$this->document->addCustomTag('<meta property="og:audio:title" content="'.$this->item->sermon_title.'"/>');
				if ($this->item->name){
					$this->document->addCustomTag('<meta property="og:audio:artist" content="'.$this->item->name.'"/>');
				}
				if ($this->item->series_title){
					$this->document->addCustomTag('<meta property="og:audio:album" content="'.$this->item->series_title.'"/>');
				}
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