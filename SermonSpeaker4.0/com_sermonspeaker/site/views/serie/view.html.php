<?php
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerie extends JViewLegacy
{
	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		if (!$app->input->get('id', 0, 'int')){
			$app->redirect(JRoute::_('index.php?view=series'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}
		// Applying CSS file
		JHTML::stylesheet('media/com_sermonspeaker/css/sermonspeaker.css');
		require_once(JPATH_COMPONENT.'/helpers/player.php');
		// Initialise variables.
		$user		= JFactory::getUser();
		// Get some data from the model
		$this->item	= $this->get('Item');
		if(!$this->item){
			$app->redirect(JRoute::_('index.php?view=series'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}
		// check if access is not public
		if ($this->item->category_access){
			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($this->item->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=series'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}
		// Get Params
		$state		= $this->get('State');
		$this->params = $state->get('params');
		// Get sermons data from the sermons model
		$sermon_model		= $this->getModel('Sermons');
		$sermon_model->setState('serie.id', $state->get('serie.id'));
		$this->state		= $sermon_model->getState();
		$this->items		= $sermon_model->getItems();
		$this->pagination	= $sermon_model->getPagination();
		$this->years		= $sermon_model->getYears();
		$this->months		= $sermon_model->getMonths();
		$books				= $sermon_model->getBooks();
		// Get Category stuff from models
		$this->category		= $sermon_model->getCategory();
//		$children			= $sermon_model->getChildren();
		$this->parent		= $sermon_model->getParent();
//		$this->children		= array($this->category->id => $children);
// We don't use childrens here because counting isn't accurate without added series filter.
		$this->columns	= $this->params->get('col');
		if (!$this->columns){
			$this->columns = array();
		}
		$this->col_serie = $this->params->get('col_serie');
		if (!$this->col_serie){
			$this->col_serie = array();
		}
		if (in_array('series:speaker', $this->col_serie)){
			$model		= $this->getModel();
			$speakers	= $model->getSpeakers($this->item->id);
			$popup = array();
			foreach($speakers as $speaker){
				if ($speaker->state){
					$popup[] = SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->slug, $speaker->pic, $speaker->name);
				} else {
					$popup[] = $speaker->name;
				}
			}
			$this->item->speakers = implode(', ', $popup);
		}
		// Update Statistic
		if ($this->params->get('track_series')) {
			if (!$user->authorise('com_sermonspeaker.hit', 'com_sermonspeaker')){
				$model 	= $this->getModel();
				$model->hit();
			}
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
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
			$this->setLayout($this->params->get('serielayout', 'table'));
		}
		// Build Books
		$at	= 0;
		$nt	= 0;
		$ap	= 0;
		$this->books	= array();
		$this->books[]	= JHtml::_('select.option', '0', JText::_('COM_SERMONSPEAKER_SELECT_BOOK'));
		foreach ($books as $book){
			if(!$at && $book <= 39){
				$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'));
				$at	= 1;
			} elseif($book > 39){
				if($at == 1){
					$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'));
					$at	= 2;
				}
				if(!$nt && $book <= 66){
					$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'));
					$nt	= 1;
				} elseif($book > 66){
					if($nt == 1){
						$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'));
						$nt	= 2;
					}
					if(!$ap){
						$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_APOCRYPHA'));
						$ap	= 1;
					}
				}
			}
			$object	= new stdClass;
			$object->value	= $book;
			$object->text	= JText::_('COM_SERMONSPEAKER_BOOK_'.$book);
			$this->books[]	= $object;
		}
		if($at == 1){
			$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'));
		} elseif($nt == 1){
			$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'));
		} elseif($ap == 1){
			$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_APOCRYPHA'));
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
		$pathway = $app->getPathway();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		// if the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'serie' || $menu->query['id'] != $this->item->id))
		{
			if($this->item->series_title)
			{
				$title = $this->item->series_title;
			}
		}

		// Check for empty title and add site name if param is set
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
		if (empty($title))
		{
			$title = $this->item->series_title;
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->series_title);

		// Set MetaData
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		// Add Metadata for Facebook Open Graph API
		$fbadmins	= $this->params->get('fbadmins', '');
		$fbapp_id	= $this->params->get('fbapp_id', '');
		if ($fbadmins || $fbapp_id)
		{
			$this->document->addCustomTag('<meta property="og:title" content="'.$this->item->series_title.'"/>');
			$this->document->addCustomTag('<meta property="og:url" content="'.JURI::getInstance()->toString().'"/>');
			$this->document->addCustomTag('<meta property="og:description" content="'.$this->document->getDescription().'"/>');
			$this->document->addCustomTag('<meta property="og:site_name" content="'.$app->getCfg('sitename').'"/>');
			$this->document->addCustomTag('<meta property="og:type" content="article"/>');
			if ($this->item->avatar)
			{
				$this->document->addCustomTag('<meta property="og:image" content="'.SermonSpeakerHelperSermonSpeaker::makelink($this->item->avatar, true).'"/>');
			}
		}
	}
}