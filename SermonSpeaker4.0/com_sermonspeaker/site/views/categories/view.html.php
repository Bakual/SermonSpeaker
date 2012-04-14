<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class SermonspeakerViewCategories extends JView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
		// Initialise variables
		$this->state	= $this->get('State');
		$items			= $this->get('Items');
		$this->parent	= $this->get('Parent');
		$this->items	= array($this->parent->id => $items);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		if (($this->items === false) || ($this->parent == false)){
			JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			return false;
		}

		$this->params	= &$this->state->params;

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($this->params->get('categorieslayout', 'normal'));
		}

		//Escape strings for HTML output
		$this->pageclass_sfx	= htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->maxLevelcat		= $this->params->get('maxLevelcat', -1);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JCATEGORY'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
