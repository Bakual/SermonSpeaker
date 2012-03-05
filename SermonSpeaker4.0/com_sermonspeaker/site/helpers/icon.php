<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.helper');
/**
 * Sermonspeaker Component HTML Helper
 */
class JHtmlIcon
{
	static function edit($item, $params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();

		// Ignore if Frontend Uploading is disabled
		if ($params && !$params->get('fu_enable')) {
			return;
		}

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($item->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		switch ($attribs['type']){
			default:
			case 'sermon':
				$view	= 'frontendupload';
				break;
			case 'serie':
				$view	= 'serieform';
				break;
			case 'speaker':
				$view	= 'speakerform';
				break;
		}
			
		$url	= 'index.php?option=com_sermonspeaker&task='.$view.'.edit&s_id='.$item->id.'&return='.base64_encode($uri);
		$icon	= $item->state ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image', 'system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($item->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		if($item->created != '0000-00-00 00:00:00'){
			$date = JHtml::_('date', $item->created);
			$overlib .= '&lt;br /&gt;';
			$overlib .= JText::sprintf('JGLOBAL_CREATED_DATE_ON', $date);
		}
		if($item->author){
			$overlib .= '&lt;br /&gt;';
			$overlib .= JText::_('JAUTHOR').': '.htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8');
		}

		$button = JHtml::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('JACTION_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
