<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewSeriessermon extends JView
{
	function display($tpl = null)
	{
		// Applying CSS file
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app		= JFactory::getApplication();
		$params		= $app->getParams();
//		$user	=& JFactory::getUser();
//		$document =& JFactory::getDocument();

		$columns = $params->get('col');
		if (!$columns){
			$columns = array();
		}

		// Check to see if the user has access to view the sermon
/*		$aid	= $user->get('aid'); // 0 = public, 1 = registered, 2 = special

		if ($params->get('access') > $aid){
			if (!$aid){
				// Redirect to login
				$uri	= JFactory::getURI();
				$return	= $uri->toString();

				$url  = 'index.php?option=com_user&view=login&return='.base64_encode($return);

				//$url	= JRoute::_($url, false);
				$app->redirect($url, JText::_('You must login first'));
			} else {
				JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
				return;
			}
		}
*/

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE').' | '.$document->getTitle());
		$document->setMetaData("description",JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE'));
		$document->setMetaData("keywords",JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add swfobject-javascript for player if needed
		if (in_array('seriessermon:player', $columns)){
			if ($params->get('alt_player')){
				$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/audio_player/audio-player.js");
				$document->addScriptDeclaration('
				AudioPlayer.setup("'.JURI::root().'components/com_sermonspeaker/media/player/audio_player/player.swf", {
					width: 290,
					initialvolume: 100,
					transparentpagebg: "yes",
					left: "000000",
					lefticon: "FFFFFF"
				});');
			} else {
				$document->addScript(JURI::root()."components/com_sermonspeaker/media/player/swfobject.js");
			}
		}
		
 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$temp_item->params = clone($params);
		JPluginHelper::importPlugin('content');
		foreach($items as $item){
			// Trigger Event for `series_description`
			$temp_item->text	= &$row->series_description;
			$dispatcher->trigger('onPrepareContent', array(&$temp_item, &$temp_item->params, 0));
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('serie',		$serie);
		$this->assignRef('cat',			$cat);
		$this->assignRef('columns', 	$columns);

		parent::display($tpl);
	}	
}