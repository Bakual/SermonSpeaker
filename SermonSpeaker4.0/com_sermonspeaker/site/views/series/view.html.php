<?php
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeries extends JViewLegacy
{
	function display($tpl = null)
	{
		// Get some data from the models
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		// Get Category stuff from models
		$this->category		= $this->get('Category');
		$children			= $this->get('Children');
		$this->parent		= $this->get('Parent');
		$this->children		= array($this->category->id => $children);
		$this->params		= $this->state->get('params');
		$this->col_serie	= $this->params->get('col_serie');
		if (!$this->col_serie){
			$this->col_serie = array();
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		// getting the Speakers for each Series and check if there are avatars at all, only showing column if needed
		$this->av = NULL;
		$model = $this->getModel();
		foreach ($this->items as $item){
			if (!$this->av && !empty($item->avatar)){
				$this->av = 1;
			}
			if (in_array('series:speaker', $this->col_serie)){
				$speakers	= $model->getSpeakers($item->id);
				$popup	= array();
				$names	= array();
				foreach($speakers as $speaker){
					if ($speaker->state){
						$popup[]	= SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->slug, $speaker->pic, $speaker->name);
					} else {
						$popup[]	= $speaker->name;
					}
					$names[]		= $speaker->name;
				}
				$item->speakers	= implode(', ', $popup);
				$item->names	= implode(', ', $names);
			}
		}
		if ($this->category == false) {
			return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}
		if ($this->parent == false && $this->category->id != 'root') {
				return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}
		if ($this->category->id == 'root'){
			$this->params->set('show_category_title', 0);
			$this->cat = '';
		} else {
			// Get the category title for backward compatibility
			$this->cat = $this->category->title;
		}
		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($this->category->access, $groups)) {
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($this->params->get('serieslayout', 'normal'));
		}
		$this->pageclass_sfx	= htmlspecialchars($this->params->get('pageclass_sfx'));
		$this->maxLevel			= $this->params->get('maxLevel', -1);
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

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}