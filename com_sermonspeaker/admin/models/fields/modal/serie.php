<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;

/**
 * Supports a modal serie picker.
 *
 * @since ?
 */
class JFormFieldModal_Serie extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since   1.6
	 */
	protected $type = 'Modal_Serie';

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
		$isSite = Factory::getApplication()->isClient('site');

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
		$modalPrefix = $isSite ? 'Serieform' : 'Serie';
		$modalId     = $modalPrefix . '_' . $this->id;

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
				window.jSelectSerie_" . $this->id . " = function (id, title, catid, object, url, language) {
					window.processModalSelect('" . $modalPrefix . "', '" . $this->id . "', id, title, catid, object, url, language);
				}",
					[],
					['type' => 'module']
				);

				Text::script('JGLOBAL_ASSOCIATIONS_PROPAGATE_FAILED');

				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display.
		$linkSeries = 'index.php?option=com_sermonspeaker&amp;view=series&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$linkSerie  = 'index.php?option=com_sermonspeaker&amp;view=serie&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
		$controller = $isSite ? 'serieform' : 'serie';
		$urlVar     = $isSite ? 's_id' : 'id';

		if (isset($this->element['language']))
		{
			$linkSeries .= '&amp;forcedLanguage=' . $this->element['language'];
			$linkSerie  .= '&amp;forcedLanguage=' . $this->element['language'];
			$modalTitle = Text::_('COM_SERMONSPEAKER_CHANGE_SERIE') . ' &#8212; ' . $this->element['label'];
		}
		else
		{
			$modalTitle = Text::_('COM_SERMONSPEAKER_CHANGE_SERIE');
		}

		$urlSelect = $linkSeries . '&amp;function=jSelectSerie_' . $this->id;
		$urlEdit   = $linkSerie . '&amp;task=' . $controller . '.edit&amp;' . $urlVar . '=\' + document.getElementById(&quot;' . $this->id . '_id&quot;).value + \'';
		$urlNew    = $linkSerie . '&amp;task=' . $controller . '.add';

		$db = Factory::getDbo();

		if ($value === '' && !$this->element['ignoredefault'] && !$this->form->getValue('id'))
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__sermon_series'))
				->where($db->quoteName('home') . ' = 1');
			$db->setQuery($query);

			try
			{
				$value = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		if ($value)
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__sermon_series'))
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

		$title = empty($title) ? Text::_('COM_SERMONSPEAKER_SELECT_A_SERIE') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current serie display field.
		$html = '';

		if ($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35">';

		// Select serie button
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

		// New serie button
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

		// Edit serie button
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

		// Clear serie button
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

		// Propagate serie button
		if ($allowPropagate && count($languages) > 2)
		{
			// Strip off language tag at the end
			$tagLength            = (int) strlen($this->element['language']);
			$callbackFunctionStem = substr("jSelectSerie_" . $this->id, 0, -$tagLength);

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

		// Select serie modal
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

		// New serie modal
		if ($allowNew)
		{
			$footer = '<button type="button" class="btn btn-secondary"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'cancel\', \'item-form\'); return false;">'
				. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<button role="button" class="btn btn-primary"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'save\', \'item-form\'); return false;">'
				. Text::_('JSAVE') . '</button>';

			if (!$isSite)
			{
				$footer .= '<button type="button" class="btn btn-success"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'apply\', \'item-form\'); return false;">'
					. Text::_('JAPPLY') . '</button>';
			}

			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalNew' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_NEW_SERIE'),
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

		// Edit serie modal
		if ($allowEdit)
		{
			$footer = '<button type="button" class="btn btn-secondary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'cancel\', \'item-form\'); return false;">'
				. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<button type="button" class="btn btn-primary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'save\', \'item-form\'); return false;">'
				. Text::_('JSAVE') . '</button>';

			if (!$isSite)
			{
				$footer .= '<button type="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'apply\', \'item-form\'); return false;">'
					. Text::_('JAPPLY') . '</button>';
			}

			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalEdit' . $modalId,
				array(
					'title'       => Text::_('COM_SERMONSPEAKER_EDIT_SERIE'),
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
			. '" data-text="' . htmlspecialchars(Text::_('COM_SERMONSPEAKER_SELECT_A_SERIE', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '" />';

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
