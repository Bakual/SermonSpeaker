<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeries extends JView
{
	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$col_serie = $params->get('col_serie');
		if (!$col_serie){
			$col_serie = array();
		}

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Get the category name(s)
		if($state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// getting the Speakers for each Series and check if there are avatars at all, only showing column if needed
		$av = NULL;
		$model = $this->getModel();
		foreach ($items as $item){
			if (!$av && !empty($item->avatar)){
				$av = 1;
			}
			if (in_array('series:speaker', $col_serie)){
				$speakers	= $model->getSpeakers($item->id);
				$popup = array();
				foreach($speakers as $speaker){
					$popup[] = SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->slug, $speaker->pic, $speaker->name);
				}
				$item->speakers = implode(', ', $popup);
			}
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('serieslayout', 'normal'));
		}

		// push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('av',			$av);			// for Avatars
		$this->assignRef('cat',			$cat);			// for Category title
		$this->assignRef('col_serie', 	$col_serie);

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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_SERIES_TITLE');
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