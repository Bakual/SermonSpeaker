<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewArchive extends JView
{
	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$years		= $this->get('Years');
		$months		= $this->get('Months');

		// Add filter to pagination
		$pagination->setAdditionalUrlParam('view', 'archive');
		$pagination->setAdditionalUrlParam('year', $state->get('date.year'));
		$pagination->setAdditionalUrlParam('month', $state->get('date.month'));

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Create Daterange
		if ($state->get('date.month')){
			$date = $state->get('date.year').'-'.$state->get('date.month').'-15';
			$date_format = 'F, Y';
		} else {
			$date = $state->get('date.year').'-01-15';
			$date_format = 'Y';
		}
		$range = JText::sprintf('COM_SERMONSPEAKER_ARCHIVE_RANGE', JHTML::Date($date, $date_format, 'UTC'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('archivelayout', 'table'));
		}

		// push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('cat',			$cat);
		$this->assignRef('range',		$range);
		$this->assignRef('years',		$years);
		$this->assignRef('months',		$months);

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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_ARCHIVE_TITLE');
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// add Breadcrumbs
//		$pathway = $app->getPathway();
//		$pathway->addItem($this->serie->series_title);

		// Set MetaData from menu entry if available
		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}
	}
}