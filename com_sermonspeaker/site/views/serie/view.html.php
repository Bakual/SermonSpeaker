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
		JHtml::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
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
		$this->parent		= $sermon_model->getParent();
		// Add filter to pagination, needed since it's no longer stored in userState.
		$this->pagination->setAdditionalUrlParam('year', $this->state->get('date.year'));
		$this->pagination->setAdditionalUrlParam('month', $this->state->get('date.month'));

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
					$popup[] = SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->slug, $speaker->pic, $speaker->speaker_title);
				} else {
					$popup[] = $speaker->speaker_title;
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
		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($this->params->get('serielayout', 'table'));
		}
		$js = 'function clear_all(){
			if(document.getElementById(\'filter_books\')){
				document.getElementById(\'filter_books\').value=0;
			}
			if(document.getElementById(\'filter_months\')){
				document.getElementById(\'filter_months\').value=0;
			}
			if(document.getElementById(\'filter_years\')){
				document.getElementById(\'filter_years\').value=0;
			}
			if(document.getElementById(\'filter-search\')){
				document.getElementById(\'filter-search\').value="";
			}
		}';
		$this->document->addScriptDeclaration($js);
		// Build Books
		$groups			= array();
		foreach ($books as $book)
		{
			switch ($book)
			{
				case ($book < 40):
					$group	= 'OLD_TESTAMENT';
					break;
				case ($book < 67):
					$group	= 'NEW_TESTAMENT';
					break;
				case ($book < 74):
					$group	= 'APOCRYPHA';
					break;
				default:
					$group	= 'CUSTOMBOOKS';
					break;
			}

			$object					= new stdClass;
			$object->value			= $book;
			$object->text			= JText::_('COM_SERMONSPEAKER_BOOK_'.$book);
			$groups[$group][]	= $object;
		}
		foreach ($groups as $key => &$group)
		{
			array_unshift($group, JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_'.$key)));
			array_push($group, JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_'.$key)));
		}
		// needs PHP 5.3.0
//		$this->books	= array_reduce($groups, 'array_merge', array());
		// PHP 5.2 compatible.
		$this->books	= array();
		if (isset($groups['OLD_TESTAMENT']))
		{
			$this->books	= $groups['OLD_TESTAMENT'];
		}
		if (isset($groups['NEW_TESTAMENT']))
		{
			$this->books	= array_merge($this->books, $groups['NEW_TESTAMENT']);
		}
		if (isset($groups['APOCRYPHA']))
		{
			$this->books	= array_merge($this->books, $groups['APOCRYPHA']);
		}
		if (isset($groups['CUSTOMBOOKS']))
		{
			$this->books	= array_merge($this->books, $groups['CUSTOMBOOKS']);
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
		if ($this->params->get('opengraph', 1))
		{
			$this->document->addCustomTag('<meta property="og:title" content="'.$this->escape($this->item->series_title).'"/>');
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