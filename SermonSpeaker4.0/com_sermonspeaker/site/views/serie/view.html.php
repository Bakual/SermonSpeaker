<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerie extends JView
{
	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		if (!JRequest::getInt('id', 0)){
			$app->redirect(JRoute::_('index.php?view=series'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

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
		$this->cat			= $sermon_model->getCat();
		$this->years		= $sermon_model->getYears();
		$this->months		= $sermon_model->getMonths();
		$books				= $sermon_model->getBooks();

		$this->columns	= $this->params->get('col');
		if (!$this->columns){
			$this->columns = array();
		}
		$this->col_serie = $this->params->get('col_serie');
		if (!$this->col_serie){
			$this->col_serie = array();
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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
		}

		// Set Pagetitle
		if ($this->item->series_title && (!$menu || $menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'serie' || $menu->query['id'] != $this->item->id)){
			$title = $this->item->series_title;
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->series_title);

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
		if ($app->getCfg('MetaTitle')){
			$this->document->setMetaData('title', $this->item->series_title);
		}
	}
}