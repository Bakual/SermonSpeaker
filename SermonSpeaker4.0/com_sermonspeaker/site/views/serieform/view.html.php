<?php
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerieform extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;
	function display($tpl = null)
	{
		JHTML::stylesheet('frontendupload.css', 'media/com_sermonspeaker/css/');
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
		// Create a shortcut to the parameters.
		$params	= &$this->state->params;
		if (empty($this->item->id)) {
			$authorised = ($params->get('fu_enable') && $user->authorise('core.create', 'com_sermonspeaker'));
		} else {
			$authorised = ($params->get('fu_enable') && $user->authorise('core.edit', 'com_sermonspeaker'));
		}
		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		$this->params	= $params;
		$this->user		= $user;
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
			$this->params->def('page_heading', JText::_('JEDITOR'));
		}
		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('JEDITOR');
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