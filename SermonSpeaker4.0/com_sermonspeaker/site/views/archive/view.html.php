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
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

 		$cat = NULL;
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	=& $this->get('Cat'); // TODO: missing in model
			$cat	= ': '.$cat;
		}

		// Create title
		if ($state->get('date.month')){
			$date = $state->get('date.year').'-'.$state->get('date.month');
			$date_format = 'F, Y';
		} else {
			$date = $state->get('date.year').'-01';
			$date_format = 'Y';
		}
		$title = JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE').' '.JHTML::date($date, $date_format, false).$cat;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$temp_item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Loop through each item and create links
		$direct_link = $params->get('list_direct_link', '00');
		foreach($items as $item){
			// Trigger Event for `sermon_scripture`
			$temp_item->text	= &$item->sermon_scripture;
			$dispatcher->trigger('onPrepareContent', array(&$temp_item, &$temp_item->params, 0));
			switch ($direct_link){ // direct links to the file instead to the detailpage
				case '00':
					$item->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					$item->link2 = $item->link1;
					break;
				case '01':
					$item->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					$item->link2 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					break;
				case '10':
					$item->link1 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					$item->link2 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					break;
				case '11':
					$item->link1 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					$item->link2 = $item->link1;
					break;
			}
		}

       // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
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