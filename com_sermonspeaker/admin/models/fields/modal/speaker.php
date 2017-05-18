<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Supports a modal speaker picker.
 *
 * @since ?
 */
class JFormFieldModal_Speaker extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since   1.6
	 */
	protected $type = 'Modal_Speaker';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 * @throws \Exception
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$allowNew    = ((string) $this->element['new'] == 'true');
		$allowEdit   = ((string) $this->element['edit'] == 'true');
		$allowClear  = ((string) $this->element['clear'] != 'false');
		$allowSelect = ((string) $this->element['select'] != 'false');

		// Load language
		JFactory::getLanguage()->load('com_sermonspeaker', JPATH_ADMINISTRATOR);

		// The active speaker id field.
		$value = (int) $this->value > 0 ? (int) $this->value : '';

		// Create the modal id.
		$modalId = 'Speaker_' . $this->id;

		// Add the modal field script to the document head.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/modal-fields.js', array('version' => 'auto', 'relative' => true));

		// Script to proxy the select modal function to the modal-fields.js file.
		if ($allowSelect)
		{
			static $scriptSelect = null;

			if (is_null($scriptSelect))
			{
				$scriptSelect = array();
			}

			if (!isset($scriptSelect[$this->id]))
			{
				JFactory::getDocument()->addScriptDeclaration("
				function jSelectSpeaker_" . $this->id . "(id, title, catid, object, url, language) {
					window.processModalSelect('Speaker', '" . $this->id . "', id, title, catid, object, url, language);
				}
				");

				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display.
		$linkSpeakers = 'index.php?option=com_sermonspeaker&amp;view=speakers&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$linkSpeaker  = 'index.php?option=com_sermonspeaker&amp;view=speaker&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		if (isset($this->element['language']))
		{
			$linkSpeakers .= '&amp;forcedLanguage=' . $this->element['language'];
			$linkSpeaker  .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle    = JText::_('COM_SERMONSPEAKER_CHANGE_SPEAKER') . ' &#8212; ' . $this->element['label'];
		}
		else
		{
			$modalTitle    = JText::_('COM_SERMONSPEAKER_CHANGE_SPEAKER');
		}

		$urlSelect = $linkSpeakers . '&amp;function=jSelectSpeaker_' . $this->id;
		$urlEdit   = $linkSpeaker . '&amp;task=speaker.edit&amp;id=\' + document.getElementById("' . $this->id . '_id").value + \'';
		$urlNew    = $linkSpeaker . '&amp;task=speaker.add';

		if ($value)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__sermon_speakers'))
				->where($db->quoteName('id') . ' = ' . (int) $value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}
		}

		$title = empty($title) ? JText::_('COM_SERMONSPEAKER_SELECT_A_SPEAKER') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current speaker display field.
		$html  = '<span class="input-append">';
		$html .= '<input class="input-medium" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35" />';

		// Select speaker button
		if ($allowSelect)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalSelect' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_CHANGE_SPEAKER') . '">'
				. '<span class="icon-file" aria-hidden="true"></span> ' . JText::_('JSELECT')
				. '</a>';
		}

		// New speaker button
		if ($allowNew)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_new"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalNew' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_NEW_SPEAKER') . '">'
				. '<span class="icon-new" aria-hidden="true"></span> ' . JText::_('JACTION_CREATE')
				. '</a>';
		}

		// Edit speaker button
		if ($allowEdit)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_edit"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalEdit' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_EDIT_SPEAKER') . '">'
				. '<span class="icon-edit" aria-hidden="true"></span> ' . JText::_('JACTION_EDIT')
				. '</a>';
		}

		// Clear speaker button
		if ($allowClear)
		{
			$html .= '<a'
				. ' class="btn' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_clear"'
				. ' href="#"'
				. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
				. '<span class="icon-remove" aria-hidden="true"></span>' . JText::_('JCLEAR')
				. '</a>';
		}

		$html .= '</span>';

		// Select speaker modal
		if ($allowSelect)
		{
			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalSelect' . $modalId,
				array(
					'title'       => $modalTitle,
					'url'         => $urlSelect,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'footer'      => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
				)
			);
		}

		// New speaker modal
		if ($allowNew)
		{
			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalNew' . $modalId,
				array(
					'title'       => JText::_('COM_SERMONSPEAKER_NEW_SPEAKER'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlNew,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'footer'      => '<a role="button" class="btn" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'cancel\', \'item-form\'); return false;">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
						. '<a role="button" class="btn btn-primary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'save\', \'item-form\'); return false;">'
						. JText::_('JSAVE') . '</a>'
						. '<a role="button" class="btn btn-success" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'apply\', \'item-form\'); return false;">'
						. JText::_('JAPPLY') . '</a>',
				)
			);
		}

		// Edit speaker modal
		if ($allowEdit)
		{
			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalEdit' . $modalId,
				array(
					'title'       => JText::_('COM_SERMONSPEAKER_EDIT_SPEAKER'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlEdit,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'footer'      => '<a role="button" class="btn" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'speaker\', \'cancel\', \'item-form\'); return false;">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
						. '<a role="button" class="btn btn-primary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'speaker\', \'save\', \'item-form\'); return false;">'
						. JText::_('JSAVE') . '</a>'
						. '<a role="button" class="btn btn-success" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'speaker\', \'apply\', \'item-form\'); return false;">'
						. JText::_('JAPPLY') . '</a>',
				)
			);
		}

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(JText::_('COM_SERMONSPEAKER_SELECT_A_SPEAKER', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '" />';

		return $html;
	}
}
