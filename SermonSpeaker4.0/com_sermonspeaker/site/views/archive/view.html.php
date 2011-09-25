<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewArchive extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$years		= $this->get('Years');
		$months		= $this->get('Months');

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Create Daterange
		if ($state->get('date.month')){
			$date = $state->get('date.year').'-'.$state->get('date.month').'-15';
			$date_format = 'F, Y';
		} else {
			$date = $state->get('date.year').'-01-15';
			$date_format = 'Y';
		}
		$range = JText::sprintf('COM_SERMONSPEAKER_ARCHIVE_RANGE', JHTML::Date($date, $date_format, 'UTC'));

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
		$this->assignRef('cat',			$cat);
		$this->assignRef('range',		$range);
		$this->assignRef('years',		$years);
		$this->assignRef('months',		$months);

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
		if (in_array('archive:player', $this->columns) && count($this->items)){
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
					$width = $this->params->get('mp_width', '100%');
					if (is_numeric($width)){ $width .= 'px'; }
					$height = $this->params->get('mp_height', '400px');
					if (is_numeric($height)){ $height .= 'px'; }
					$this->document->addScriptDeclaration('
						function Video() {
							jwplayer().load(['.$this->player->playlist['video'].']).resize("'.$width.'","'.$height.'");
							document.getElementById("mediaspace1_wrapper").style.width="'.$width.'";
							document.getElementById("mediaspace1_wrapper").style.height="'.$height.'";
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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE');
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
//		$pathway = $app->getPathway();
//		$pathway->addItem($this->serie->series_title);

		// Set MetaData from menu entry if available
		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}
	}
}