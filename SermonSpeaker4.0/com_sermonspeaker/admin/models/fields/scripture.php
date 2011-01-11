<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Scripture Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldScripture extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Scripture';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$params	= &JComponentHelper::getParams('com_sermonspeaker');
		$start 	= $params->get('plugin_open');
		$end	= $params->get('plugin_close');
		
		$html 	= '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" class="inputbox" />'
				. '<img title="insert Plugin tag" alt="insert Plugin tag" onClick="sendText(document.adminForm.jform_sermon_scripture,\''
				. $start.'\',\''.$end
				. '\')" src="'.JURI::root().'/components/com_sermonspeaker/images/blue_tag.png">';

		return $html;
	}
}
