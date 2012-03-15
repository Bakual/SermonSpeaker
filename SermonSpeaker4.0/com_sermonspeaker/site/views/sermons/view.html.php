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
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		$params = $state->get('params');
		if ((int)$params->get('limit', '')){
			$params->set('filter_field', 0);
			$params->set('show_pagination_limit', 0);
			$params->set('show_pagination', 0);
			$params->set('show_pagination_results', 0);
		}
		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat = $this->get('Cat');
		} else {
			$cat = '';
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('sermonslayout', 'table'));
		}

		// Build Books
		$this->books	= array();
		$this->books[]	= JHtml::_('select.option', '0', JText::_('COM_SERMONSPEAKER_SELECT_BOOK'));
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'));
		for ($i = 1; $i < 40; $i++){
			$books_at[$i]->value	= $i;
			$books_at[$i]->text		= JText::_('COM_SERMONSPEAKER_BOOK_'.$i);
		}
		$this->books	= array_merge($this->books, $books_at);
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_OLD_TESTAMENT'));
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'));
		for (; $i < 67; $i++){
			$books_nt[$i]->value	= $i;
			$books_nt[$i]->text		= JText::_('COM_SERMONSPEAKER_BOOK_'.$i);
		}
		$this->books	= array_merge($this->books, $books_nt);
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_NEW_TESTAMENT'));
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_APOCRYPHA'));
		for (; $i < 74; $i++){
			$books_ap[$i]->value	= $i;
			$books_ap[$i]->text		= JText::_('COM_SERMONSPEAKER_BOOK_'.$i);
		}
		$this->books	= array_merge($this->books, $books_ap);
		$this->books[]	= JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_APOCRYPHA'));

		// push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('cat',			$cat);
		$this->assignRef('params',		$params);

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