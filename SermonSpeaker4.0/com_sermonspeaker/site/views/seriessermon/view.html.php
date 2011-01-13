<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeriessermon extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();
//		$user	=& JFactory::getUser();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// check if access is not public
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		if (!in_array($params->get('access'), $groups)) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$temp_item->params = clone($params);
		JPluginHelper::importPlugin('content');
		foreach($items as $item){
			// Trigger Event for `series_description`
			$temp_item->text	= &$row->series_description;
			$dispatcher->trigger('onPrepareContent', array(&$temp_item, &$temp_item->params, 0));
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('serie',		$serie);
		$this->assignRef('cat',			$cat);
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

		// Add swfobject-javascript for player if needed
		if (in_array('seriessermon:player', $this->columns)){
			if ($params->get('alt_player')){
				$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/audio_player/audio-player.js");
				$document->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'components/com_sermonspeaker/media/player/audio_player/player.swf", {
					width: 290,
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			} else {
				$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
			}
		}
		
		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE'));
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description = $description.' ';
		}
		$this->document->setMetaData('description', $description.JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE'));

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE'));
	}
}