<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Tag Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldTag extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Tag';

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
		if (!isset($this->value[0])){
			$this->value[0] = '';
		}
		if (!isset($this->value[1])){
			$this->value[1] = '';
		}
		$html 	= '<input type="text" size="10" name="'.$this->name.'" id="'.$this->id.'_start" value="'.htmlspecialchars($this->value[0], ENT_COMPAT, 'UTF-8').'" class="inputbox" />'
				. '<span class="faux-label" style="clear:none; min-width:0px; margin-left:2px; margin-right:2px;">John 3,16</span>'
				. '<input type="text" size="5" name="'.$this->name.'" id="'.$this->id.'_end" value="'.htmlspecialchars($this->value[1], ENT_COMPAT, 'UTF-8').'" class="inputbox" />'
				;

		return $html;
	}
}
