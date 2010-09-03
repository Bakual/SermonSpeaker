<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

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
	 * Method to get the field Label.
	 *
	 * @return	array	The field Label.
	 * @since	1.6
	 */
	protected function getLabel()
	{
		$html[] = '<table style="width:100%;">';
		$html[] = '<thead><tr>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">View</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Num</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Series</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Scripture</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Notes</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Date</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">Length</th>';
		$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">AddFile</th>';
		$html[] = '</tr></thead>';

		return implode($html);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// define the columns
		$columns = array(
			'Num',
			'Series',
			'Scripture',
			'Notes',
			'Date',
			'Length',
			'AddFile'
		);
		
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes '.(string) $this->element['class'].'"' : ' class="checkboxes"';

		// Initialize JavaScript field attributes.
		$onclick	= $this->element['onclick'] ? ' onclick="'.(string) $this->element['onclick'].'"' : '';

		// Start the table body.
		$html[] = '<tbody>';
		
		// Get the field options.
		$options = $this->getOptions();

		// Build the table rows.
		foreach ($options as $i => $option) {

			// Initialize some option attributes.
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
			$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';

			// Initialize some JavaScript option attributes.
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<tr>';
			$html[] = '<th style="height: 25px; background: #F4F4F4; border-bottom: 1px solid silver; vertical-align:middle;">'.$option->text.'</th>';
			foreach ($columns as $column){
				// Initialize some option attributes.
				$checked	= (in_array((string)$option->value.'['.$column.']', (array)$this->value) ? ' checked="checked"' : '');
				$html[] = '<td align="center">';
				$html[] = '<input style="float:none; margin:0;" type="checkbox" id="'.$this->id.$i.'" name="'.$this->name.'"' .
						' value="'.htmlspecialchars($option->value.'['.$column.']', ENT_COMPAT, 'UTF-8').'"'.$checked.$class.$onclick.$disabled.'/>';
				$html[] = '</td>';
			}
			$html[] = '</tr>';
		}

		// End the table body.
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
			$tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled']=='true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
