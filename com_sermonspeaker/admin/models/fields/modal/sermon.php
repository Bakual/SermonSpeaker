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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

/**
 * Supports a modal sermon picker.
 *
 * @since ?
 */
class JFormFieldModal_Sermon extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since   1.6
	 */
	protected $type = 'Modal_Sermon';

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
		$allowNew       = ((string) $this->element['new'] == 'true');
		$allowEdit      = ((string) $this->element['edit'] == 'true');
		$allowClear     = ((string) $this->element['clear'] != 'false');
		$allowSelect    = ((string) $this->element['select'] != 'false');
		$allowPropagate = ((string) $this->element['propagate'] == 'true');

		$languages = LanguageHelper::getContentLanguages(array(0, 1), false);

		// Load language
		Factory::getLanguage()->load('com_sermonspeaker', JPATH_ADMINISTRATOR);

		// The active serie id field.
		$value = (int) $this->value ?: '';

		// Create the modal id.
		$modalId = 'Sermon_' . $this->id;

		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Add the modal field script to the document head.
		$wa->useScript('field.modal-fields');

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
				$wa->addInlineScript("
				window.jSelectSermon_" . $this->id . " = function (id, title, catid, object, url, language) {
					window.processModalSelect('Sermon', '" . $this->id . "', id, title, catid, object, url, language);
				}",
					[],
					['type' => 'module']
				);

				Text::script('JGLOBAL_ASSOCIATIONS_PROPAGATE_FAILED');

				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display.
		$linkSermons = 'index.php?option=com_sermonspeaker&amp;view=sermons&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$linkSermon  = 'index.php?option=com_sermonspeaker&amp;view=sermon&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		if (isset($this->element['language']))
		{
			$linkSermons .= '&amp;forcedLanguage=' . $this->element['language'];
			$linkSermon  .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle  = Text::_('COM_SERMONSPEAKER_CHANGE_SERMON') . ' &#8212; ' . $this->element['label'];
		}
		else
		{
			$modalTitle = Text::_('COM_SERMONSPEAKER_CHANGE_SERMON');
		}

		$urlSelect = $linkSermons . '&amp;function=jSelectSermon_' . $this->id;
		$urlEdit   = $linkSermon . '&amp;task=sermon.edit&amp;id=\' + document.getElementById("' . $this->id . '_id").value + \'';
		$urlNew    = $linkSermon . '&amp;task=sermon.add';

		if ($value)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__sermon_sermons'))
				->where($db->quoteName('id') . ' = :value')
				->bind(':value', $value, ParameterType::INTEGER);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		$title = empty($title) ? Text::_('COM_SERMONSPEAKER_SELECT_A_SERMON') : htmlspecialchars($title, ENT_QUOTES);

		// The current sermon display field.
		$html = '';

		if ($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35">';

		// Select sermon button
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

		// New sermon button
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

		// Edit sermon button
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

		// Clear sermon button
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

		// Propagate sermon button
		if ($allowPropagate && count($languages) > 2)
		{
			// Strip off language tag at the end
			$tagLength            = (int) strlen($this->element['language']);
			$callbackFunctionStem = substr("jSelectSermon_" . $this->id, 0, -$tagLength);

			$html .= '<button'
				. ' class="btn btn-primary' . ($value ? '' : ' hidden') . '"'
				. ' type="button"'
				. ' id="' . $this->id . '_propagate"'
				. ' title="' . Text::_('JGLOBAL_ASSOCIATIONS_PROPAGATE_TIP') . '"'
				. ' onclick="Joomla.propagateAssociation(\'' . $this->id . '\', \'' . $callbackFunctionStem . '\');">'
				. '<span class="icon-sync" aria-hidden="true"></span> ' . Text::_('JGLOBAL_ASSOCIATIONS_PROPAGATE_BUTTON')
				. '</button>';
		}

		if ($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '</span>';
		}

		// Select sermon modal
		if ($allowSelect)
		{
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalSelect' . $modalId,
				array(
					'title'      => $modalTitle,
					'url'        => $urlSelect,
					'height'     => '400px',
					'width'      => '800px',
					'bodyHeight' => 70,
					'modalWidth' => 80,
					'footer'     => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
						. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
				)
			);
		}

		// New sermon modal
		if ($allowNew)
		{
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalNew' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_NEW_SERMON'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlNew,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<button type="button" class="btn btn-secondar"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'sermon\', \'cancel\', \'item-form\'); return false;">'
						. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'sermon\', \'save\', \'item-form\'); return false;">'
						. Text::_('JSAVE') . '</button>'
						. '<button type="button" class="btn btn-success"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'sermon\', \'apply\', \'item-form\'); return false;">'
						. Text::_('JAPPLY') . '</button>',
				)
			);
		}

		// Edit sermon modal
		if ($allowEdit)
		{
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalEdit' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_EDIT_SERMON'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlEdit,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<button type="button" class="btn btn-secondar"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'sermon\', \'cancel\', \'item-form\'); return false;">'
						. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'sermon\', \'save\', \'item-form\'); return false;">'
						. Text::_('JSAVE') . '</button>'
						. '<button type="button" class="btn btn-success"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'sermon\', \'apply\', \'item-form\'); return false;">'
						. Text::_('JAPPLY') . '</button>',
				)
			);
		}

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_SERMONSPEAKER_SELECT_A_SERMON', true), ENT_COMPAT) . '" value="' . $value . '" />';

		return $html;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   6.0
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_name', parent::getLabel());
	}
}
