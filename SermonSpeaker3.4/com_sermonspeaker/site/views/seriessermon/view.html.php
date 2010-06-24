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
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$app 	=& JFactory::getApplication();
		$user	=& JFactory::getUser();
		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Check to see if the user has access to view the sermon
		$aid	= $user->get('aid'); // 0 = public, 1 = registered, 2 = special

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

		// Set Meta
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ' | ' ." ". JText::_('SERIESSERMONS'));
		$document->setMetaData("description",JText::_('SERIESSERMONS'));
		$document->setMetaData("keywords",JText::_('SERIESSERMONS'));


		// get Data from Model (/models/seriessermon.php)
        $rows		=& $this->get('Data');			// getting the Datarows from the Model
        $pagination	=& $this->get('Pagination');	// getting the JPaginationobject from the Model

 		$cat = NULL;
		if($params->get('series_cat') || $params->get('speaker_cat') || $params->get('sermon_cat')){
			$cat	=& $this->get('Cat');
			$cat	= ': '.$cat;
		}

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		$item->params = clone($params);
		JPluginHelper::importPlugin('content');
		foreach($rows as $row){
			// Trigger Event for `series_description`
			$item->text	= &$row->series_description;
			$dispatcher->trigger('onPrepareContent', array(&$item, &$item->params, 0));
		}

       // push data into the template
		$this->assignRef('rows',$rows);             
		$this->assignRef('pagination',$pagination);	// for JPagination
		$this->assignRef('params',$params);			// for Params
		$this->assignRef('cat',$cat);				// for Category title

		parent::display($tpl);
	}	
}