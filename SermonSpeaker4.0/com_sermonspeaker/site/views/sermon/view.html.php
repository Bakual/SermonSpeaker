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
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

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

		//Check if link targets to an external source
		if ($params->get('fileprio')){
			$lnk = SermonspeakerHelperSermonspeaker::makelink($item->videofile);
		} else {
			$lnk = SermonspeakerHelperSermonspeaker::makelink($item->audiofile);
		}

		// get active View from Menuitem
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		if ($menu){
			$menu_view = $menu->query['view'];
		} else {
			$menu_view = '';
		}

		// add Breadcrumbs
		$breadcrumbs	= $app->getPathWay();
		if ($menu_view == 'series') {
	    	$breadcrumbs->addItem($serie->series_title, JRoute::_(SermonspeakerHelperRoute::getSerieRoute($serie->slug)));
		} elseif ($menu_view == 'speakers') {
	    	$breadcrumbs->addItem($speaker->name, JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($speaker->slug)));
		}
    	$breadcrumbs->addItem($item->sermon_title, '');

		// Process the content plugins.
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$item->text = &$item->notes;
		$results = $dispatcher->trigger('onContentPrepare', array ('com_sermonspeaker.sermon', &$item, &$this->params));
		$item->text = &$item->sermon_scripture;
		$results = $dispatcher->trigger('onContentPrepare', array ('com_sermonspeaker.sermon', &$item, &$this->params));

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
		$this->assignRef('lnk', 		$lnk);
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

		// Add swfobject-javascript for player if needed
		if (in_array('sermon:player', $this->columns) || JRequest::getCmd('layout', '') == 'popup'){
			if ($this->params->get('alt_player')){
				$this->document->addScript(JURI::root()."components/com_sermonspeaker/media/player/audio_player/audio-player.js");
				$this->document->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'components/com_sermonspeaker/media/player/audio_player/player.swf", {
					width: 290,
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			} else {
//				$this->document->addScript(JURI::root().'components/com_sermonspeaker/media/player/jwplayer/swfobject.js');
				$this->document->addScript(JURI::root().'components/com_sermonspeaker/media/player/jwplayer/jwplayer.js');
			}
		}
		
		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, $this->item->sermon_title);
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description .= ' ';
		}
		if ($this->item->metadesc) {
			$description .= $this->item->metadesc;
		} else {
			$description .= strip_tags($this->item->notes);
		}
		$this->document->setMetaData('description', $description);

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords .= ', ';
		}
		if ($this->item->metakey) {
			$keywords .= $this->item->metakey;
		} else {
			$keywords .= $this->escape(str_replace(' ', ',', $this->item->sermon_title).','.str_replace(',', ':', $this->item->sermon_scripture));
		}
		$this->document->setMetaData('keywords', $keywords);
	}
}