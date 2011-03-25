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
		JHTML::stylesheet('sermonspeaker.css', 'media/com_sermonspeaker/css/');

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

		// Get the category name(s)
		if($state->get('sermons_category.id') || $state->get('speakers_category.id') || $state->get('series_category.id')){
			$cat	= $this->get('Cat');
		} else {
			$cat 	= '';
		}
		
		foreach($items as $i => $item){
			// Prepare play icon function
			switch ($params->get('list_icon_function', 3)){
				case 0:
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
					$item->link1 = JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $pic);
					break;
				case 1:
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
					$item->link1 = JHTML::Link(SermonspeakerHelperSermonspeaker::makelink($item->audiofile), $pic);
					break;
				case 2:
					$options['onClick'] = 'jwplayer().playlistItem('.$i.')';
					$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$options['class'] = 'icon_play pointer';
					$item->link1 = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'), $options);
					break;
				case 3:
					$options['onClick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$params->get('popup_height').',width='.$params->get('mp_width').",scrollbars=yes,resizable=yes'); return false";
					$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
					$options['class'] = 'icon_play pointer';
					$item->link1 = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_POPUPPLAYER'), $options);
					break;
			}
			// Prepare title link function
			switch ($params->get('list_title_function', 0)){
				case 0:
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$item->link2 = JHTML::Link(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)), $item->sermon_title, $options);
					break;
				case 1:
					$options['title'] = JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER');
					$pic = JHTML::Image('media/com_sermonspeaker/images/play.gif', JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'), $options);
					$item->link2 = JHTML::Link(SermonspeakerHelperSermonspeaker::makelink($item->audiofile), $item->sermon_title, $options);
					break;
				case 2:
					$options['onClick'] = 'jwplayer().playlistItem('.$i.')';
					$options['title'] = JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER');
					$item->link2 = JHTML::Link('#', $item->sermon_title, $options);
					break;
				case 3:
					$options['onClick'] = "popup=window.open('".JRoute::_('index.php?view=sermon&layout=popup&id='.$item->id.'&tmpl=component')."', 'PopupPage', 'height=".$params->get('popup_height').',width='.$params->get('mp_width').",scrollbars=yes,resizable=yes'); return false";
					$options['title'] = JText::_('COM_SERMONSPEAKER_POPUPPLAYER');
					$item->link2 = JHTML::Link('#', $item->sermon_title, $options);
					break;
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

        // push data into the template
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('columns', 	$columns);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('cat',			$cat);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();

		// Add javascript for player if needed
		if (in_array('sermons:player', $this->columns)){
			JHTML::Script('media/com_sermonspeaker/player/jwplayer/jwplayer.js', true);
			$this->player = SermonspeakerHelperSermonspeaker::insertPlayer($this->items);
			if($this->params->get('fileswitch')){
				$this->document->addScriptDeclaration('
					function Video() {
						jwplayer().load(['.$this->player['video-pl'].']);
					}
				');
				$this->document->addScriptDeclaration('
					function Audio() {
						jwplayer().load(['.$this->player['audio-pl'].']);
					}
				');
			}
		}

		// Set Pagetitle
		$title 	= $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$title = JText::sprintf('JPAGETITLE', $title, JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
		$this->document->setTitle($title);

		// Set MetaData
		$description = $this->document->getMetaData('description');
		if ($description){
			$description .= ' ';
		}
		$this->document->setMetaData('description', $description.JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));

		$keywords = $this->document->getMetaData('keywords');
		if ($keywords){
			$keywords = $keywords.', ';
		}
		$this->document->setMetaData('keywords', $keywords.JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
	}
}