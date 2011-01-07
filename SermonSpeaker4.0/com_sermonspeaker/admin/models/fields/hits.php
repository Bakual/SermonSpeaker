<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Hits Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldHits extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Hits';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$onclick	= ' onclick="document.id(\''.$this->id.'\').value=\'0\';"';
		
		$return = '<input style="border:0;" type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" readonly="readonly" class="readonly" size="5" />';
		if ($this->value){
			$return .= '<img src="'.JURI::base().'components/com_sermonspeaker/images/reset.png"'.$onclick.' width="16" height="16" border="0" title="'.JText::_('JSEARCH_RESET').'" alt="Reset" />';
		}

		return $return;
	}
}
