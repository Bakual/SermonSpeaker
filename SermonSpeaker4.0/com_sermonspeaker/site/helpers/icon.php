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

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($item->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		$url	= 'index.php?option=com_sermonspeaker&task=frontendupload.edit&s_id='.$item->id.'&return='.base64_encode($uri);
		$icon	= $item->state ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image', 'system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($item->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date', $item->created);
//		$author = $item->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
//		$overlib .= '&lt;br /&gt;';
//		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button = JHtml::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('JACTION_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
