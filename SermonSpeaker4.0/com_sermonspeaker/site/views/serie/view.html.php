<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSerie extends JView
{
	function display($tpl = null)
	{
		if (!JRequest::getInt('id', 0)){
			JError::raiseWarning(404, JText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
			return;
		}

		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$serie		= $this->get('Serie');
		$pagination	= $this->get('Pagination');

		// add Breadcrumbs
		$breadcrumbs	= &$app->getPathWay();
		$breadcrumbs->addItem($serie->series_title);

		// Update Statistic
		if ($params->get('track_series')) {
			$model = $this->getModel();
			$model->hit();
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `series_description`
		$item->text	= &$serie->series_description;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

		// Loop through each item and create links
		$direct_link = $params->get('list_direct_link', '00');
		foreach($items as $row){
			switch ($direct_link){ // direct links to the file instead to the detailpage
				case '00':
					$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
					$row->link2 = $row->link1;
					break;
				case '01':
					$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
					$row->link2 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
					break;
				case '10':
					$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
					$row->link2 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
					break;
				case '11':
					$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->audiofile);
					$row->link2 = $row->link1;
					break;
			}
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('serie',		$serie);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();

		// Add swfobject-javascript for player if needed
		if (in_array('sermon:player', $this->columns)){
//			$this->document->addScript(JURI::root().'components/com_sermonspeaker/media/player/jwplayer/swfobject.js');
			$this->document->addScript(JURI::root().'components/com_sermonspeaker/media/player/jwplayer/jwplayer.js');
		}
		
		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description .= ' ';
		}
		$this->document->setMetaData('description', $description.JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
	}
}