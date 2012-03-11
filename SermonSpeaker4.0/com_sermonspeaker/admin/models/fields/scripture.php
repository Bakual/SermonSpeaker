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
class JFormFieldScripture extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Scripture';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

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
			if ($value['book']){
				$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
				$text		= JText::_('COM_SERMONSPEAKER_BOOK_'.$value['book']);
				if ($value['cap1']){
					$text .= ' '.$value['cap1'];
					if ($value['vers1']){
						$text .= $separator.$value['vers1'];
					}
					if ($value['cap2'] || $value['vers2']){
						$text .= '-';
						if ($value['cap2']){
							$text .= $value['cap2'];
							if ($value['vers2']){
								$text .= $separator.$value['vers2'];
							}
						} else {
							$text .= $value['vers2'];
						}
					}
				}
			} else {
				$text	= $value['text'];
				$title	= 'old" title="'.JText::_('COM_SERMONSPEAKER_SCRIPTURE_NOT_SEARCHABLE');
			}
			$html .= '<span id="scripture_span_'.$i.'">';
			$html .= '<input id="'.$this->id.'_'.$i.'" type="hidden" value="'.implode('|',$value).'" name="'.$this->name.'['.$i.']">';
			$html .= '<img class="pointer" onclick="delete_scripture('.$i.');" src="'.JURI::root().'media/com_sermonspeaker/images/delete.png"> ';
			$html .= '<input id="'.$this->id.'_text_'.$i.'" class="readonly scripture'.$title.'" '.$class.$size.$disabled.$readonly.$maxLength.' value="'.$text.'" name="jform['.$this->fieldname.'_text]['.$i.']" />';
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
