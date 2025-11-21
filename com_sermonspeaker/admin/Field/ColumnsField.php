<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * Columns Field class for the SermonSpeaker
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class ColumnsField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Columns';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var        boolean
	 * @since    1.6
	 */
	protected bool $forceMultiple = true;

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput(): string
	{
		// Get the field options.
		$options = $this->getOptions();

		// Get the field columns.
		$columns = explode(',', (string) $this->element['cols']);

		// Start the table.
		$html[] = '<table class="table table-sm align-middle">';
		$html[] = '<thead><tr>';
		$html[] = '<th>View</th>';
		foreach ($columns as $column)
		{
			$label  = ($column == 'category') ? 'JCATEGORY' : 'COM_SERMONSPEAKER_FIELD_' . strtoupper($column) . '_LABEL';
			$html[] = '<th class="text-center">' . Text::_($label) . '</th>';
		}
		$html[] = '</tr></thead>';
		$html[] = '<tbody>';

		// Build the table datarows.
		foreach ($options as $i => $option)
		{

			// Initialize some option attributes.
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			$html[] = '<tr>';
			$html[] = '<th>' . $option->text . '</th>';
			foreach ($columns as $column)
			{
				// Initialize some option attributes.
				$checked  = (in_array($option->value . ':' . $column, (array) $this->value) ? ' checked="checked"' : '');
				$disabled = in_array($column, (array) $option->exclude) ? ' disabled="disabled"' : '';

				$html[] = '<td class="text-center">';
				$html[] = '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' .
					' value="' . htmlspecialchars($option->value . ':' . $column, ENT_COMPAT) . '"' . $checked . $class . $onclick . $disabled . '/>';
				$html[] = '</td>';
			}
			$html[] = '</tr>';
		}

		// End the table.
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	protected function getOptions(): array
	{
		// Initialize variables.
		$options = array();

		/** @var \SimpleXMLElement $option */
		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = HTMLHelper::_('select.option', (string) $option['value'], Text::_(trim((string) $option)), 'value', 'text', ((string) $option['disabled'] == 'true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Get the Excludes
			$excludes     = !empty($option['exclude']) ? explode(',', $option['exclude']) : array();
			$tmp->exclude = $excludes;

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		return $options;
	}

	protected function getColumns(): array
	{
		// define the columns
		return array(
			'num'       => 'COM_SERMONSPEAKER_FIELD_NUM',
			'scripture' => 'COM_SERMONSPEAKER_FIELD_SCRIPTURE',
			'speaker'   => 'COM_SERMONSPEAKER_FIELD_SPEAKER',
			'date'      => 'COM_SERMONSPEAKER_FIELD_DATE',
			'length'    => 'COM_SERMONSPEAKER_FIELD_LENGTH',
			'series'    => 'COM_SERMONSPEAKER_FIELD_SERIES',
			'addfile'   => 'COM_SERMONSPEAKER_FIELD_ADDFILE',
			'notes'     => 'COM_SERMONSPEAKER_FIELD_NOTES',
			'player'    => 'COM_SERMONSPEAKER_FIELD_PLAYER',
			'hits'      => 'COM_SERMONSPEAKER_FIELD_HITS',
		);
	}
}
