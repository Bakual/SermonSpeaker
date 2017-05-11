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
		// Add the modal field script to the document head.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/fields/modal-fields.js', array('version' => 'auto', 'relative' => true));

		// Initialize some field attributes.
		$size      = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$readonly  = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled  = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		$app        = JFactory::getApplication();
		$document   = JFactory::getDocument();
		$javascript = "function delete_scripture(id){
			var child = document.getElementById('scripture_span_'+id)
			document.getElementById('scripture_span').removeChild(child);
		}";
		$document->addScriptDeclaration($javascript);
		$admin = $app->isClient('administrator');

		if ($admin)
		{
			$url = 'index.php?option=com_sermonspeaker&view=scripture&tmpl=component';
		}
		else
		{
			$url = JRoute::_('index.php?option=com_sermonspeaker&view=scripture&tmpl=component');
		}

		$modalId = 'scriptureModal_' . $this->id;
		$html    = '<span id="scripture_span">';
		$i       = 1;

		foreach ($this->value as $value)
		{
			$title = '';

			if ($value['text'])
			{
				$text = $value['text'];

				if (!$value['book'])
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
			$html .= '<input id="' . $this->id . '_' . $i . '" type="hidden" value="' . implode('|', $value) . '" name="' . $this->name . '[' . $i . ']">';
			$html .= '<div class="input-group">';
			$html .= '<span class="input-group-btn">';
			$html .= '<button class="btn btn-secondary" onclick="delete_scripture(' . $i . ');"><span class="icon-trash"></span></button>';
			$html .= '</span>';

			$html .= '<input id="' . $this->id . '_text_' . $i . '" type="text" class="readonly scripture pointer' . $title . '" '
							. 'data-toggle="modal" data-target="#' . $modalId . $i . '" '
							. $size . $disabled . $readonly . $maxLength . ' value="' . $text . '" name="jform[' . $this->fieldname . '_text][' . $i . ']" />';

			$html .= '</div>';

			$html .= JHtml::_(
				'bootstrap.renderModal',
				$modalId . $i,
				array(
					'title'       => JText::_('COM_SERMONSPEAKER_EDIT_SCRIPTURE'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $url . '&id=' . $i,
					'height'      => '400px',
					'width'       => '100%',
					'modalWidth'  => 50,
					'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
						. ' onclick="jQuery(\'#' . $modalId . $i . '\').modal(\'hide\'); return false;">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
						. '<a role="button" class="btn btn-primary" aria-hidden="true"'
						. ' onclick="jQuery(\'#' . $modalId . $i . ' .iframe\')[0].contentWindow.AddScripture();'
						. ' jQuery(\'#' . $modalId . $i . '\').modal(\'hide\'); return false;">'
						. JText::_('JSAVE') . '</a>',
				)
			);


			if (!$admin)
			{
				$html .= '<br />';
			}

			$html .= '<label></label> </span>';
			$i++;
		}

		$html .= '<input type="hidden" id="scripture_id" value="' . $i . '" /></span>';

		$html .= '<a href="#' . $modalId . '"
						class="btn btn-secondary hasTooltip"
						title="' . JText::_('COM_SERMONSPEAKER_NEW_SCRIPTURE') . '"
						data-toggle="modal"
						role="button"
					>
						<span class="icon-new"></span>
					</a>';

		$html .= JHtml::_(
			'bootstrap.renderModal',
			$modalId,
			array(
				'title'       => JText::_('COM_SERMONSPEAKER_NEW_SCRIPTURE'),
				'backdrop'    => 'static',
				'keyboard'    => false,
				'closeButton' => false,
				'url'         => $url,
				'height'      => '400px',
				'width'       => '100%',
				'modalWidth'  => 50,
				'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
					. ' onclick="jQuery(\'#' . $modalId . '\').modal(\'hide\'); return false;">'
					. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
					. '<a role="button" class="btn btn-primary" aria-hidden="true"'
					. ' onclick="jQuery(\'#' . $modalId . ' .iframe\')[0].contentWindow.AddScripture();'
						. ' jQuery(\'#' . $modalId . '\').modal(\'hide\'); return false;">'
					. JText::_('JSAVE') . '</a>',
			)
		);

		return $html;
	}
}
