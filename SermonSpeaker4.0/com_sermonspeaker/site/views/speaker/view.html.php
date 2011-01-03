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
		$params		= $app->getParams();
		
		if ($this->getLayout() == 'default') {
			$this->setLayout('series');
		} 

		$model = $this->getModel();
		$model->setState('speaker.layout', $this->getLayout());

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$speaker	= $this->get('Speaker');
		$pagination	= $this->get('Pagination');

		// add breadcrumbs and page title according to chosen layout
		$document 	=& JFactory::getDocument();
		$breadcrumbs	= &$app->getPathWay();
		if ($this->getLayout() == "latest-sermons") {
		  	if ($params->get('limit_speaker') == 1) {
				$limit = $app->getCfg('list_limit');
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONSOF', $limit, $speaker->name);
				$bread = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_LATESTSERMONS', $limit);
			} else {
				$title = JText::sprintf('COM_SERMONSPEAKER_SPEAKER_SERMONSOF', $speaker->name);
				$bread = JText::_('COM_SERMONSPEAKER_SERMONS');
			}
			$breadcrumbs->addItem($speaker->name.': '.$bread, '');
			$document->setTitle($title.' | '.$document->getTitle());
		} elseif ($this->getLayout() == "popup") {
			$title = $speaker->name;
		} else {
			// check if there are avatars at all, only showing column if needed
			$av = null;
			foreach ($items as $item){
				if (!empty($item->avatar)){ // breaking out of foreach if first avatar is found
					$av = 1;
					break;
				}
			}
			$this->assignRef('av', $av);
			$document->setTitle(JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE').' - '.$speaker->name.' | '.$document->getTitle());
			$breadcrumbs->addItem($speaker->name.': '.JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'), '');
			$title = $speaker->name;
		}

		// Set Meta
		$document->setMetaData("description", strip_tags($speaker->intro));
		$document->setMetaData("keywords", $title);

		// Add swfobject-javascript for player
		$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
		
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		// Update Statistic
		if ($params->get('track_speaker')) {
			$model = $this->getModel();
			$model->hit();
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Check whether category access level allows access.
/*		$user	= JFactory::getUser();
		$groups	= $user->authorisedLevels();
		if (!in_array($category->access, $groups)) {
			return JError::raiseError(403, JText::_("JERROR_ALERTNOAUTHOR"));
		}
*/

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Trigger Event for `intro`
		$item->text	= &$speaker->intro;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		// Trigger Event for `bio`
		$item->text	= &$speaker->bio;
		$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));

		if ($this->getLayout() == "latest-sermons"){
			$direct_link = $params->get('list_direct_link', '00');
			foreach($items as $row){
				// Trigger Event for `sermon_scripture`
				$item->text	= &$row->sermon_scripture;
				$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
				switch ($direct_link){ // direct links to the file instead to the detailpage
					case '00':
						$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						$row->link2 = $row->link1;
						break;
					case '01':
						$row->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						$row->link2 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
						break;
					case '10':
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
						$row->link2 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
						break;
					case '11':
						$row->link1 = SermonspeakerHelperSermonspeaker::makelink($row->sermon_path);
						$row->link2 = $row->link1;
						break;
				}
			}
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('speaker',		$speaker);
		$this->assignRef('title',		$title);

		parent::display($tpl);
	}
}