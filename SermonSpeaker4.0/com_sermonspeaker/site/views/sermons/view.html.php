<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSermons extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('COM_SERMONSPEAKER_SERMONS_TITLE').' | '.$document->getTitle());
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));

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
		$temp_item->params = clone($params);
		JPluginHelper::importPlugin('content');
		// Loop through each item and create links
		$direct_link = $params->get('list_direct_link', '00');
		foreach($items as $item){
			$item->link1 = '1';
			// Trigger Event for `sermon_scripture`
			$temp_item->text	= &$item->sermon_scripture;
			$dispatcher->trigger('onPrepareContent', array(&$temp_item, &$temp_item->params, 0));
			switch ($direct_link){ // direct links to the file instead to the detailpage
				case '00':
					$item->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					$item->link2 = $item->link1;
					break;
				case '01':
					$item->link1 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					$item->link2 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					break;
				case '10':
					$item->link1 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					$item->link2 = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					break;
				case '11':
					$item->link1 = SermonspeakerHelperSermonspeaker::makelink($item->sermon_path);
					$item->link2 = $item->link1;
					break;
			}
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}