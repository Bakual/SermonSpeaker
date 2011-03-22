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

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Create title
		if ($state->get('date.month')){
			$date = $state->get('date.year').'-'.$state->get('date.month').'-15';
			$date_format = 'F, Y';
		} else {
			$date = $state->get('date.year').'-01-15';
			$date_format = 'Y';
		}
		$title = JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE').' '.JHTML::Date($date, $date_format, 'UTC').$cat;

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
		if (in_array('archive:player', $this->columns)){
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
		
		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, $this->title);
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description .= ' ';
		}
		$this->document->setMetaData('description', $description.$this->title);

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
	}
}