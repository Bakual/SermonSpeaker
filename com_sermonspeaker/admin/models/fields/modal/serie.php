<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();


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
		$isSite = JFactory::getApplication()->isClient('site');

		$allowNew    = ((string) $this->element['new'] == 'true');
		$allowEdit   = ((string) $this->element['edit'] == 'true');
		$allowClear  = ((string) $this->element['clear'] != 'false');
		$allowSelect = ((string) $this->element['select'] != 'false');

		// Load language
		JFactory::getLanguage()->load('com_sermonspeaker', JPATH_ADMINISTRATOR);

		// The active serie id field.
		$value = (int) $this->value > 0 ? (int) $this->value : '';

		// Create the modal id.
		$modalPrefix = $isSite ? 'Serieform' : 'Serie';
		$modalId     = $modalPrefix . '_' . $this->id;

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
				function jSelectSerie_" . $this->id . "(id, title, catid, object, url, language) {
					window.processModalSelect('" . $modalPrefix . "', '" . $this->id . "', id, title, catid, object, url, language);
				}
				");

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
			$modalTitle = JText::_('COM_SERMONSPEAKER_CHANGE_SERIE') . ' &#8212; ' . $this->element['label'];
		}
		else
		{
			$modalTitle = JText::_('COM_SERMONSPEAKER_CHANGE_SERIE');
		}

		$urlSelect = $linkSeries . '&amp;function=jSelectSerie_' . $this->id;
		$urlEdit   = $linkSerie . '&amp;task=' . $controller . '.edit&amp;' . $urlVar . '=\' + document.getElementById("' . $this->id . '_id").value + \'';
		$urlNew    = $linkSerie . '&amp;task=' . $controller . '.add';

		$db = JFactory::getDbo();

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
				JError::raiseWarning(500, $e->getMessage());
			}
		}

		if ($value)
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__sermon_series'))
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

		$title = empty($title) ? JText::_('COM_SERMONSPEAKER_SELECT_A_SERIE') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current serie display field.
		$html = '<span class="input-append">';
		$html .= '<input class="input-medium" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35" />';

		// Select serie button
		if ($allowSelect)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalSelect' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_CHANGE_SERIE') . '">'
				. '<span class="icon-file" aria-hidden="true"></span> ' . JText::_('JSELECT')
				. '</a>';
		}

		// New serie button
		if ($allowNew)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_new"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalNew' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_NEW_SERIE') . '">'
				. '<span class="icon-new" aria-hidden="true"></span> ' . JText::_('JACTION_CREATE')
				. '</a>';
		}

		// Edit serie button
		if ($allowEdit)
		{
			$html .= '<a'
				. ' class="btn hasTooltip' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_edit"'
				. ' data-toggle="modal"'
				. ' role="button"'
				. ' href="#ModalEdit' . $modalId . '"'
				. ' title="' . JHtml::tooltipText('COM_SERMONSPEAKER_EDIT_SERIE') . '">'
				. '<span class="icon-edit" aria-hidden="true"></span> ' . JText::_('JACTION_EDIT')
				. '</a>';
		}

		// Clear serie button
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

		// Select serie modal
		if ($allowSelect)
		{
			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalSelect' . $modalId,
				array(
					'title'      => $modalTitle,
					'url'        => $urlSelect,
					'height'     => '400px',
					'width'      => '800px',
					'bodyHeight' => '70',
					'modalWidth' => '80',
					'footer'     => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
				)
			);
		}

		// New serie modal
		if ($allowNew)
		{
			$footer = '<a role="button" class="btn" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'cancel\', \'adminForm\'); return false;">'
				. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<a role="button" class="btn btn-primary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'save\', \'adminForm\'); return false;">'
				. JText::_('JSAVE') . '</a>';

			if (!$isSite)
			{
				$footer .= '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'' . $controller . '\', \'apply\', \'adminForm\'); return false;">'
					. JText::_('JAPPLY') . '</a>';
			}

			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalNew' . $modalId,
				array(
					'title'       => JText::_('COM_SERMONSPEAKER_NEW_SERIE'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlNew,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'footer'      => $footer,
				)
			);
		}

		// Edit serie modal
		if ($allowEdit)
		{
			$footer = '<a role="button" class="btn" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'cancel\', \'adminForm\'); return false;">'
				. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
				. '<a role="button" class="btn btn-primary" aria-hidden="true"'
				. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'save\', \'adminForm\'); return false;">'
				. JText::_('JSAVE') . '</a>';

			if (!$isSite)
			{
				$footer .= '<a role="button" class="btn btn-success" aria-hidden="true"'
					. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'' . $controller . '\', \'apply\', \'adminForm\'); return false;">'
					. JText::_('JAPPLY') . '</a>';
			}

			$html .= JHtml::_(
				'bootstrap.renderModal',
				'ModalEdit' . $modalId,
				array(
					'title'       => JText::_('COM_SERMONSPEAKER_EDIT_SERIE'),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $urlEdit,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'footer'      => $footer,
				)
			);
		}

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(JText::_('COM_SERMONSPEAKER_SELECT_A_SERIE', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '" />';

		return $html;
	}
}
