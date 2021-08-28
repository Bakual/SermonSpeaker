<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

jimport('joomla.form.formfield');

/**
 * Scripture Field class for the SermonSpeaker
 *
 * @since ?
 */
class JFormFieldScripture extends FormField
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
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Add the modal field script to the document head.
		$wa->useScript('field.modal-fields');

		// Initialize some field attributes.
		$size      = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$readonly  = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled  = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		$app = Factory::getApplication();

		$wa->addInlineScript("
		function delete_scripture(id){
			var child = document.getElementById('scripture_span_'+id)
			document.getElementById('scripture_span').removeChild(child);
		}");

		$admin = $app->isClient('administrator');

		if ($admin)
		{
			$url = 'index.php?option=com_sermonspeaker&view=scripture&tmpl=component';
		}
		else
		{
			$url = Route::_('index.php?option=com_sermonspeaker&view=scripture&tmpl=component');
		}

		$modalId = 'scriptureModal_' . $this->id;
		$html    = '<span id="scripture_span">';
		$i       = 1;

		foreach ($this->value as $value)
		{
			$title = '';
			$class = '';

			if ($value['text'])
			{
				$text = $value['text'];

				if (!$value['book'])
				{
					$class = ' old hasTooltip';
					$title = ' title="' . Text::_('COM_SERMONSPEAKER_SCRIPTURE_NOT_SEARCHABLE') . '"';
				}
			}
			else
			{
				$separator = Text::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
				$text      = Text::_('COM_SERMONSPEAKER_BOOK_' . $value['book']);

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

			$html .= '<span id="scripture_span_' . $i . '">
						<input id="' . $this->id . '_' . $i . '" type="hidden" value="' . implode('|', $value) . '" name="' . $this->name . '[' . $i . ']">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-secondary" onclick="delete_scripture(' . $i . ')"><span class="fas fa-trash"></span></button>
							</span>
							<input id="' . $this->id . '_text_' . $i . '" type="text" class="readonly form-control scripture' . $class . '"' . $title
				. 'data-bs-toggle="modal" data-bs-target="#' . $modalId . $i . '" '
				. $size . $disabled . $readonly . $maxLength . ' value="' . $text . '" name="jform[' . $this->fieldname . '_text][' . $i . ']" />
						</div>';

			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				$modalId . $i,
				array(
					'title'      => Text::_('COM_SERMONSPEAKER_EDIT_SCRIPTURE'),
					'url'        => $url . '&id=' . $i,
					'height'     => '400px',
					'width'      => '100%',
					'modalWidth' => 50,
					'footer'     => '<button type="button" class="btn btn-secondary"'
						. ' onclick="jQuery(\'#' . $modalId . $i . '\').modal(\'hide\'); return false;">'
						. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary"'
						. ' onclick="jQuery(\'#' . $modalId . $i . ' .iframe\')[0].contentWindow.AddScripture();'
						. ' jQuery(\'#' . $modalId . $i . '\').modal(\'hide\'); return false;">'
						. Text::_('JSAVE') . '</button>',
				)
			);

			if (!$admin)
			{
				$html .= '<br />';
			}

			$html .= '</span>';
			$i++;
		}

		$html .= '<input type="hidden" id="scripture_id" value="' . $i . '" /></span>';

		$html .= '<button href="#' . $modalId . '"
						class="btn btn-secondary hasTooltip"
						title="' . Text::_('COM_SERMONSPEAKER_NEW_SCRIPTURE') . '"
						data-bs-toggle="modal"
						type="button"
					>
						<span class="fas fa-plus"></span>
					</button>';

		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			$modalId,
			array(
				'title'      => Text::_('COM_SERMONSPEAKER_NEW_SCRIPTURE'),
				'url'        => $url,
				'height'     => '400px',
				'width'      => '100%',
				'modalWidth' => 50,
				'footer'     => '<button type="button" class="btn btn-secondary"'
					. ' onclick="jQuery(\'#' . $modalId . '\').modal(\'hide\'); return false;">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
					. '<button type="button" class="btn btn-primary"'
					. ' onclick="jQuery(\'#' . $modalId . ' .iframe\')[0].contentWindow.AddScripture();'
					. ' jQuery(\'#' . $modalId . '\').modal(\'hide\'); return false;">'
					. Text::_('JSAVE') . '</button>',
			)
		);

		return $html;
	}
}
