<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSpeakers extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Get the category name(s)
		if($state->get('speakers_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

 		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		foreach($items as $item){
			// Trigger Event for `intro`
			$item->text	= &$item->intro;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
			// Trigger Event for `bio`
			$item->text	= &$item->bio;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		}

		// push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('av',			$av);			// for Avatars
		$this->assignRef('cat',			$cat);			// for Category title

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
		$title = JText::sprintf('JPAGETITLE', $title, JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description = $description.' ';
		}
		$this->document->setMetaData('description', $description.JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));
	}
}