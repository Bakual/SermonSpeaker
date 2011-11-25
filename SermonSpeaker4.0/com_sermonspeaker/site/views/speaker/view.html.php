<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewspeaker extends JView
{
	public function __construct($config = array()){

		parent::__construct($config);
	}

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		if (!JRequest::getInt('id', 0)){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'player.php');

		$params		= $app->getParams();
		$user		= JFactory::getUser();

		$columns = $params->get('col_speaker');
		if (!$columns){
			$columns = array();
		}
		$col_sermon = $params->get('col');
		if (!$col_sermon){
			$col_sermon = array();
		}
		$col_serie = $params->get('col_serie');
		if (!$col_serie){
			$col_serie = array();
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default') {
			$this->setLayout($params->get('speakerlayout', 'series'));
		}

		// Get data from the model
		$state		= $this->get('State');
		$item		= $this->get('Item');

		if(!$item){
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}
		// check if access is not public
		if ($item->category_access){
			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($item->category_access, $groups)) {
				$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Get sermons data from the sermons model
		$sermons				= $this->get('Items', 'Sermons');
		$this->pag_sermons		= $this->get('Pagination', 'Sermons');
		$this->state_sermons	= $this->get('State', 'Sermons');

		// Get series data from the series model
		$series					= $this->get('Items', 'Series');
		$this->pag_series		= $this->get('Pagination', 'Series');
		$this->state_series		= $this->get('State', 'Series');
		// check if there are avatars at all, only showing column if needed
		$av = 0;
		foreach ($series as $serie){
			if (!empty($serie->avatar)){ // breaking out of foreach if first avatar is found
				$av = 1;
				break;
			}
		}
		$this->assignRef('av', $av);

		// Update Statistic
		if ($params->get('track_speaker')) {
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

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);
		$this->assignRef('columns',		$columns);
		$this->assignRef('col_sermon',	$col_sermon);
		$this->assignRef('col_serie',	$col_serie);
		$this->assignRef('item',		$item);
		$this->assignRef('sermons',		$sermons);
		$this->assignRef('series',		$series);

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