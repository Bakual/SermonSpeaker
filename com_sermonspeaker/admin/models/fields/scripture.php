<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.form.formfield');

/**
 * Scripture Field class for the SermonSpeaker
 *
 * @since ?
 */
class JFormFieldScripture extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $type = 'Scripture';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since ?
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size      = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class     = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly  = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled  = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		$app        = JFactory::getApplication();
		$document   = JFactory::getDocument();
		$javascript = "function delete_scripture(id){
			var child = document.getElementById('scripture_span_'+id)
			document.getElementById('scripture_span').removeChild(child);
		}";
		$document->addScriptDeclaration($javascript);
		$admin = $app->isAdmin();

		if ($admin)
		{
			$url = 'index.php?option=com_sermonspeaker&view=scripture&tmpl=component';
		}
		else
		{
			$url = JRoute::_('index.php?option=com_sermonspeaker&view=scripture&tmpl=component');
		}

		$html = '<span id="scripture_span">';
		$i = 1;

		foreach ($this->value as $value)
		{
			$title = '';

			if ($value['text'])
			{
				$text = $value['text'];

				if(!$value['book'])
				{
					$title = 'old" title="' . JText::_('COM_SERMONSPEAKER_SCRIPTURE_NOT_SEARCHABLE');
				}
			}
			else
			{
				$separator = JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
				$text      = JText::_('COM_SERMONSPEAKER_BOOK_' . $value['book']);

				if ($value['cap1'])
				{
					$text .= ' ' . $value['cap1'];

					if ($value['vers1'])
					{
						$text .= $separator . $value['vers1'];
					}

					if ($value['cap2'] || $value['vers2'])
					{
						$text .= '-';

						if ($value['cap2'])
						{
							$text .= $value['cap2'];

							if ($value['vers2'])
							{
								$text .= $separator . $value['vers2'];
							}
						}
						else
						{
							$text .= $value['vers2'];
						}
					}
				}
			}

			$html .= '<span id="scripture_span_' . $i . '">';
			$html .= '<input id="' . $this->id . '_' . $i . '" type="hidden" value="' . implode('|',$value) . '" name="' . $this->name . '[' . $i . ']">';
			$html .= '<div class="input-prepend">';
			$html .= '<div class="btn add-on icon-trash" onclick="delete_scripture(' . $i . ');"> </div>';
			$html .= '<a class="modal" href="' . $url . '&id=' . $i . '" rel="{handler: \'iframe\', size: {x: 550, y: 420}}">';
			$html .= '<input id="' . $this->id . '_text_' . $i . '" type="text" class="readonly scripture pointer' . $title . '" '
				. $size . $disabled . $readonly . $maxLength . ' value="' . $text . '" name="jform[' . $this->fieldname . '_text][' . $i . ']" />';
			$html .= '</a>';
			$html .= '</div>';

			if (!$admin)
			{
				$html .= '<br />';
			}

			$html .= '<label></label> </span>';
			$i++;
		}

		$html .= '<input type="hidden" id="scripture_id" value="' . $i . '" /></span>';
		$html .= '<a class="modal btn btn-small" href="' . $url . '" rel="{handler: \'iframe\', size: {x: 550, y: 420}}">';
		$html .= '<div class="icon-plus-2"></div>';
		$html .= '</a>';

		return $html;
	}
}
