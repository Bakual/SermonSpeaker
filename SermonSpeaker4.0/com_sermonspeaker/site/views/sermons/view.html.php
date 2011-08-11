<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSermons extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		$params = $state->get('params');
		if ((int)$params->get('limit', '')){
			$params->set('filter_field', 0);
			$params->set('show_pagination_limit', 0);
			$params->set('show_pagination', 0);
			$params->set('show_pagination_results', 0);
		}
		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat = $this->get('Cat');
		} else {
			$cat = '';
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('cat',			$cat);
		$this->assignRef('params',		$params);

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
		if (in_array('sermons:player', $this->columns) && count($this->items)){
			require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');
			$this->player = new SermonspeakerHelperPlayer($this->params);
			JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js');
			$this->player->prepare($this->items);
			if($this->params->get('fileswitch')){
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

		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_SERMONS_TITLE');
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// Set MetaData from menu entry if available
		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}
	}
}