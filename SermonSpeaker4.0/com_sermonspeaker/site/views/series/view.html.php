<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeries extends JView
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
		$document->setTitle(JText::_('COM_SERMONSPEAKER_SERIES_TITLE').' | '.$document->getTitle());
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SERIES_TITLE'));

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

		// getting the Speakers for each Series and check if there are avatars at all, only showing column if needed
		$av = NULL;
		$model = $this->getModel();
		foreach ($items as $item){					
			if (!$av && !empty($item->avatar)){
				$av = 1;
			}
			$speakers	= $model->getSpeakers($item->id);
			$popup = array();
			foreach($speakers as $speaker){
				$popup[] = SermonspeakerHelperSermonspeaker::SpeakerTooltip($speaker->speaker_id, $speaker->pic, $speaker->name);
			}
			$item->speakers = implode(', ', $popup);
		}
		
 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

       // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('av',			$av);			// for Avatars
		$this->assignRef('cat',			$cat);			// for Category title

		parent::display($tpl);
	}
}