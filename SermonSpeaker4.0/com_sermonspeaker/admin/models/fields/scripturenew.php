<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Scripture Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldScripturenew extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Scripturenew';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$app		= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$javascript	= "function delete_scripture(id){
			var child = document.getElementById('scripture_span_'+id)
			document.getElementById('scripture_span').removeChild(child);
		}";
		$document->addScriptDeclaration($javascript);
		$admin	= $app->isAdmin();
		if ($admin){
			$url = 'index.php?option=com_sermonspeaker&view=scripture&tmpl=component';
		} else {
			$url = JRoute::_('index.php?option=com_sermonspeaker&view=scripture&tmpl=component');
		}

		$html 	= '<span id="scripture_span">';
		$i = 1;
		foreach ($this->value as $value){
			$title		= '';
			$explode	= explode(',',$value);
			if ($explode[0]){
				$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
				$text		= JText::_('COM_SERMONSPEAKER_BOOK_'.$explode[0]);
				if ($explode[1]){
					$text .= ' '.$explode[1];
				}
				if ($explode[2]){
					$text .= $separator.$explode[2];
				}
				if ($explode[3] || $explode[4]){
					$text .= '-';
					if ($explode[3]){
						$text .= $explode[3];
						if ($explode[4]){
							$text .= $separator.$explode[4];
						}
					} else {
						$text .= $explode[4];
					}
				}
			} else {
				$text	= $explode[5];
				$title	= 'old" title="'.JText::_('COM_SERMONSPEAKER_SXRIPTURE_NOT_SEARCHABLE');
			}
			$html .= '<span id="scripture_span_'.$i.'">';
			$html .= '<input id="jform_scripture_'.$i.'" type="hidden" value="'.$value.'" name="jform[scripture]['.$i.']">';
			$html .= '<img class="pointer" onclick="delete_scripture('.$i.');" src="'.JURI::root().'media/com_sermonspeaker/images/delete.png"> ';
			$html .= '<input id="jform_scripture_text_'.$i.'" class="readonly scripture'.$title.'" disabled="disabled" readonly="readonly" value="'.$text.'" name="jform[scripture_text]['.$i.']" />';
			if(!$admin){
				$html	.= '<br />';
			}
			$html .= '<label></label> </span>';
			$i++;
		}
		$html	.= '<input type="hidden" id="scripture_id" value="'.$i.'" /></span>';
		$html	.= '<a class="modal" href="'.$url.'" rel="{handler: \'iframe\', size: {x: 500, y: 200}}"><img src="'.JURI::root().'media/com_sermonspeaker/images/plus.png"></a>';

		return $html;
	}
}
