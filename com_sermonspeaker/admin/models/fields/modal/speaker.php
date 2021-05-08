<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

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
		$isSite = JFactory::getApplication()->isClient('site');

		$allowNew    = ((string) $this->element['new'] == 'true');
		$allowEdit   = ((string) $this->element['edit'] == 'true');
		$allowClear  = ((string) $this->element['clear'] != 'false');
		$allowSelect = ((string) $this->element['select'] != 'false');

		// Load language
		JFactory::getLanguage()->load('com_sermonspeaker', JPATH_ADMINISTRATOR);

		// The active speaker id field.
		$value = (int) $this->value > 0 ? (int) $this->value : '';

		// Create the modal id.
		$modalPrefix = $isSite ? 'Speakerform' : 'Speaker';
		$modalId     = $modalPrefix . '_' . $this->id;

		// Add the modal field script to the document head.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/fields/modal-fields.min.js', array('version' => 'auto', 'relative' => true));

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
					window.processModalSelect('" . $modalPrefix . "', '" . $this->id . "', id, title, catid, object, url, language);
				}
				");

				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display.
		$linkSpeakers = 'index.php?option=com_sermonspeaker&amp;view=speakers&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$linkSpeaker  = 'index.php?option=com_sermonspeaker&amp;view=speaker&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$controller   = $isSite ? 'speakerform' : 'speaker';
		$urlVar       = $isSite ? 's_id' : 'id';

		if (isset($this->element['language']))
		{
			$linkSpeakers .= '&amp;forcedLanguage=' . $this->element['language'];
			$linkSpeaker  .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle   = Text::_('COM_SERMONSPEAKER_CHANGE_SPEAKER') . ' &#8212; ' . $this->element['label'];
		}
		else
		{
			$modalTitle = Text::_('COM_SERMONSPEAKER_CHANGE_SPEAKER');
		}

		$urlSelect = $linkSpeakers . '&amp;function=jSelectSpeaker_' . $this->id;
		$urlEdit   = $linkSpeaker . '&amp;task=' . $controller . '.edit&amp;' . $urlVar . '=\' + document.getElementById("' . $this->id . '_id").value + \'';
		$urlNew    = $linkSpeaker . '&amp;task=' . $controller . '.add';

		$db = JFactory::getDbo();

		if ($value === '' && !$this->element['ignoredefault'] && !$this->form->getValue('id'))
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__sermon_speakers'))
				->where($db->quoteName('home') . ' = 1');
			$db->setQuery($query);

			try
			{
				$value = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}
		}

		if ($value)
		{
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

		$title = empty($title) ? Text::_('COM_SERMONSPEAKER_SELECT_A_SPEAKER') : htmlspecialchars($title, ENT_QUOTES);

		// The current speaker display field.
		$html  = '';

		if ($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35">';

		// Select speaker button
		if ($allowSelect)
		{
			$html .= '<button'
				. ' class="btn btn-primary' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#ModalSelect' . $modalId . '">'
				. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
				. '</button>';
		}

		// New speaker button
		if ($allowNew)
		{
			$html .= '<button'
				. ' class="btn btn-secondary' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_new"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#ModalNew' . $modalId . '">'
				. '<span class="icon-plus" aria-hidden="true"></span> ' . Text::_('JACTION_CREATE')
				. '</button>';
		}

		// Edit speaker button
		if ($allowEdit)
		{
			$html .= '<button'
				. ' class="btn btn-primary' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_edit"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#ModalEdit' . $modalId . '">'
				. '<span class="icon-pen-square" aria-hidden="true"></span> ' . Text::_('JACTION_EDIT')
				. '</button>';
		}

		// Clear speaker button
		if ($allowClear)
		{
			$html .= '<button'
				. ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_clear"'
				. ' type="button"'
				. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
				. '<span class="icon-times" aria-hidden="true"></span> ' . Text::_('JCLEAR')
				. '</button>';
		}

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
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
				)
			);
		}

		// New speaker modal
		if ($allowNew)
		{
			$footer = '<a role="button" class="btn btn-secondary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'cancel\', \'adminForm\'); return false;">'
				. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<a role="button" class="btn btn-primary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'save\', \'adminForm\'); return false;">'
				. Text::_('JSAVE') . '</a>';

			if (!$isSite)
			{
				$footer .= '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'apply\', \'adminForm\'); return false;">'
					. Text::_('JAPPLY') . '</a>';
			}

			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalNew' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_NEW_SPEAKER'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlNew,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => $footer,
				)
			);
		}

		// Edit speaker modal
		if ($allowEdit)
		{
			$footer = '<a role="button" class="btn btn-secondary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'cancel\', \'adminForm\'); return false;">'
				. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<a role="button" class="btn btn-primary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'save\', \'adminForm\'); return false;">'
				. Text::_('JSAVE') . '</a>';

			if (!$isSite)
			{
				$footer .= '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'apply\', \'adminForm\'); return false;">'
					. Text::_('JAPPLY') . '</a>';
			}

			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalEdit' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_EDIT_SPEAKER'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlEdit,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => $footer,
				)
			);
		}

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_SERMONSPEAKER_SELECT_A_SPEAKER', true), ENT_COMPAT) . '" value="' . $value . '" />';

		return $html;
	}
}
