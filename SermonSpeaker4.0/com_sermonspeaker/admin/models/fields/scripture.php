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
		$onclick	= ' onclick="document.id(\''.$this->id.'\').value=\'0\';"';
		if (JPluginHelper::isEnabled('content', 'biblelinkxt')){
			$biblelinkxt = 'title="insert Biblelink tag" alt="insert Biblelink tag"';
		} else {
			$biblelinkxt = 'class="transparent" title="insert Biblelink tag, Plugin not enabled" alt="insert Biblelink tag, Plugin not enabled"';
		}
		$onclick	= ' onclick="document.id(\''.$this->id.'\').value=\'0\';"';
		if (JPluginHelper::isEnabled('content', 'scripturelinks')){
			$scripturelink = 'title="insert ScriptureLink tag" alt="insert ScriptureLink tag"';
		} else {
			$scripturelink = 'class="transparent" title="insert ScriptureLink tag, Plugin not enabled" alt="insert ScriptureLink tag, Plugin not enabled"';
		}
		

		
		$html 	= '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" class="inputbox" />'
				. '<img '.$biblelinkxt.' onClick="sendText(document.adminForm.jform_sermon_scripture,\'{bib=\',\'}\')" src="'.JURI::root().'/components/com_sermonspeaker/images/blue_tag.png">'
				. '<img '.$scripturelink.' onClick="sendText(document.adminForm.jform_sermon_scripture,\'{bible}\',\'{/bible}\')" src="'.JURI::root().'/components/com_sermonspeaker/images/green_tag.png">';

		return $html;
	}
}
