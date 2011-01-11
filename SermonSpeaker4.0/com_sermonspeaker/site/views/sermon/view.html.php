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
		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get model data (/models/sermon.php) 
		$state = $this->get('State');
		$item = $this->get('Item');

		if ($item->alias){
			$item->slug = $item->id.':'.$item->alias;
		} else {
			$item->slug = $item->id;
		}
		
		// check if access is not public
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		// Set ROOT category to public
		if ($item->category_access === NULL){
			$item->category_access = 1;
		}

		$return = '';
		if ((!in_array($params->get('access'), $groups)) || (!in_array($item->category_access, $groups))) {
			$uri		= JFactory::getURI();
			$return		= (string)$uri;

			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		$model		= &$this->getModel();
		$speaker	= &$model->getSpeaker($item->speaker_id);	// getting the Speaker from the Model
		if (in_array('sermon:series', $columns)) {
			$serie	= &$model->getSerie($item->series_id);		// getting the Serie from the Model
			$this->assignRef('serie', $serie);
		}

		//Check if link targets to an external source
		if (substr($item->sermon_path, 0, 7) == 'http://'){
			$lnk = $item->sermon_path;
		} else {  
			$lnk = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path); 
		}
		
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

		// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$params	= $app->getParams();

		if ($this->getLayout() == "default") {
			if ($params->get('sermonlayout') == 1) { $this->setLayout('allinrow'); }
			elseif ($params->get('sermonlayout') == 2) { $this->setLayout('newline'); }
			elseif ($params->get('sermonlayout') == 3) { $this->setLayout('extnewline'); }
			elseif ($params->get('sermonlayout') == 4) { $this->setLayout('icon'); }
		} 

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

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
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Add swfobject-javascript for player if needed
		if (in_array('sermon:player', $this->columns)){
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
				$this->document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
			}
		}
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_DEFAULT_PAGE_TITLE'));
		}
		if($menu && $menu->query['view'] != 'sermon') {
			$id = (int) @$menu->query['id'];
			$path = array($this->item->sermon_title => '');
			foreach($path as $name => $link)
			{
				$pathway->addItem($title, $link);
			}
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);

		if ($menu && $menu->query['view'] != 'sermon')
		{
			$id = (int) @$menu->query['id'];
			$path = array($this->item->sermon_title => '');
			$path = array_reverse($path);
		}

		if (empty($title))
		{
			$title = $this->item->sermon_title;
			$this->document->setTitle($title);
		}


		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
	}
}