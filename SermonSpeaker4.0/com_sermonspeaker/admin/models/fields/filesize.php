<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');

/**
 * Filesize Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldFilesize extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Filesize';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$html	= '<div class="input-append">';
		$html	.= parent::getInput();

		$title	= '';
		require_once(JPATH_COMPONENT_SITE .'/helpers/sermonspeaker.php');
		$title = SermonspeakerHelperSermonspeaker::convertBytes($this->value, true, false);

		$version	= new JVersion;
		$joomla30	= $version->isCompatible(3.0);
		if ($joomla30)
		{
			$html	.= '<i class="add-on icon-help" id="'.$this->id.'_info" rel="tooltip" title="'.$title.'"> </i>';
		}
		else
		{
			$html	.= '<img src="'.JURI::base().'components/com_sermonspeaker/images/info.png" class="pointer" width="16" height="16" border="0" title="'.JText::_('JSEARCH_RESET').'" alt="Reset" />';
		}
		$html .= '</div>';

		return $html;
	}
}
