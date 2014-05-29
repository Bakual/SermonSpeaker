<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.form.formfield');

/**
 * Columns Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldColumns extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Columns';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Get the field options.
		$options = $this->getOptions();

		// Get the field columns.
		$columns = explode(',', (string)$this->element['cols']);

		// Start the table.
		$html[] = '<table style="width:100%; clear:left;">';
		$html[] = '<thead><tr>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">View</th>';
		foreach ($columns as $column){
			$label = ($column == 'category') ? 'JCATEGORY' : 'COM_SERMONSPEAKER_FIELD_'.strtoupper($column).'_LABEL';
			$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">'.JText::_($label).'</th>';
		}
		$html[] = '</tr></thead>';
		$html[] = '<tbody>';
		
		// Build the table datarows.
		foreach ($options as $i => $option) {

			// Initialize some option attributes.
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';

			// Initialize some JavaScript option attributes.
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<tr>';
			$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">'.$option->text.'</th>';
			foreach ($columns as $column){
				// Initialize some option attributes.
				$checked	= (in_array((string)$option->value.':'.$column, (array)$this->value) ? ' checked="checked"' : '');
				$disabled	= in_array($column, (array)$option->exclude) ? ' disabled="disabled"' : '';

				$html[] = '<td align="center">';
				$html[] = '<input style="float:none; margin:0;" type="checkbox" id="'.$this->id.$i.'" name="'.$this->name.'"' .
						' value="'.htmlspecialchars($option->value.':'.$column, ENT_COMPAT, 'UTF-8').'"'.$checked.$class.$onclick.$disabled.'/>';
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
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		foreach ($this->element->children() as $option) {
			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['value'], JText::_(trim((string) $option)), 'value', 'text', ((string) $option['disabled']=='true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			
			// Get the Excludes
			$excludes = explode(',', $option['exclude']);
			$tmp->exclude = $excludes;

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
	
	protected function getColumns()
	{
		// define the columns
		$columns = array(
			'num' => 'COM_SERMONSPEAKER_FIELD_NUM',
			'scripture' => 'COM_SERMONSPEAKER_FIELD_SCRIPTURE',
			'speaker' => 'COM_SERMONSPEAKER_FIELD_SPEAKER',
			'date' => 'COM_SERMONSPEAKER_FIELD_DATE',
			'length' => 'COM_SERMONSPEAKER_FIELD_LENGTH',
			'series' => 'COM_SERMONSPEAKER_FIELD_SERIES',
			'addfile' => 'COM_SERMONSPEAKER_FIELD_ADDFILE',
			'notes' => 'COM_SERMONSPEAKER_FIELD_NOTES',
			'player' => 'COM_SERMONSPEAKER_FIELD_PLAYER',
			'hits' => 'COM_SERMONSPEAKER_FIELD_HITS',
		);
		reset($columns);
		
		return $columns;
	}
}
