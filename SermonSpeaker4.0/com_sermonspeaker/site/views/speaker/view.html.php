<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewspeaker extends JView
{
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		if (!JRequest::getInt('id', 0)){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

		// Get data from the model
		$state		= $this->get('State');
		$this->item	= $this->get('Item');
		$user		= JFactory::getUser();

		$this->params = $state->get('params');
		$this->columns = $this->params->get('col_speaker');
		if (!$this->columns){
			$this->columns = array();
		}
		$this->col_sermon = $this->params->get('col');
		if (!$this->col_sermon){
			$this->col_sermon = array();
		}
		$this->col_serie = $this->params->get('col_serie');
		if (!$this->col_serie){
			$this->col_serie = array();
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($this->params->get('speakerlayout', 'series'));
		}

		if(!$this->item){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}
		// check if access is not public
		if ($this->item->category_access){
			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($this->item->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Get sermons data from the sermons model
		$sermon_model			= $this->getModel('Sermons');
		$sermon_model->setState('speaker.id', $state->get('speaker.id'));
		$this->state_sermons	= $sermon_model->getState();
		$this->sermons			= $sermon_model->getItems();
		$this->pag_sermons		= $sermon_model->getPagination();
		$this->years			= $sermon_model->getYears();
		$this->months			= $sermon_model->getMonths();
		$books					= $sermon_model->getBooks();
		// Get Category stuff from models
		$this->category			= $sermon_model->getCategory();
//		$children				= $sermon_model->getChildren();
		$this->parent			= $sermon_model->getParent();
//		$this->children			= array($this->category->id => $children);
// We don't use childrens here because counting isn't accurate without added series filter.

		// Get series data from the series model
		$series_model			= $this->getModel('Series');
		$series_model->setState('speaker.id', $state->get('speaker.id'));
		$this->series			= $series_model->getItems();
		$this->pag_series		= $series_model->getPagination();
		$this->state_series		= $series_model->getState();

		// check if there are avatars at all, only showing column if needed
		$this->av = 0;
		foreach ($this->series as $serie){
			if (!empty($serie->avatar)){ // breaking out of foreach if first avatar is found
				$this->av = 1;
				break;
			}
		}

		// Update Statistic
		if ($this->params->get('track_speaker')) {
			$user	= JFactory::getUser();
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

		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'));
		}

		// Set Pagetitle
		if ($this->item->name && (!$menu || $menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'speaker' || $menu->query['id'] != $this->item->id)){
			$title = $this->item->name;
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->name);

		// Set MetaData
		if ($this->item->metadesc){
			$this->document->setDescription($this->item->metadesc);
		} elseif ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->item->metakey){
			$this->document->setMetadata('keywords', $this->item->metakey);
		} elseif ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if ($app->getCfg('MetaAuthor')){
			$this->document->setMetaData('author', $this->item->name);
		}
	}
}