<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSermons extends JView
{
	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

		// Get some data from the models
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->years		= $this->get('Years');
		$this->months		= $this->get('Months');
		$books				= $this->get('Books');

		// Create shortcut for state
		$state	= $this->state;

		// Add filter to pagination, needed in case of URL params from module(?)
		$this->pagination->setAdditionalUrlParam('view', 'sermons');
		$this->pagination->setAdditionalUrlParam('year', $state->get('date.year'));
		$this->pagination->setAdditionalUrlParam('month', $state->get('date.month'));

		$this->params = $state->get('params');
		if ((int)$this->params->get('limit', '')){
			$this->params->set('filter_field', 0);
			$this->params->set('show_pagination_limit', 0);
			$this->params->set('show_pagination', 0);
			$this->params->set('show_pagination_results', 0);
		}
		$this->columns	= $this->params->get('col');
		if (!$this->columns){
			$this->columns = array();
		}

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$this->cat = $this->get('Cat');
		} else {
			$this->cat = '';
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($this->params->get('sermonslayout', 'table'));
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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_SERMONS_TITLE');
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