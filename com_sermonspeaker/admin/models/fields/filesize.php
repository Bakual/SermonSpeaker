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

		$html	.= '<i class="add-on icon-help" id="'.$this->id.'_info" rel="tooltip" title="'.$title.'"> </i>';
		$html	.= '</div>';

		return $html;
	}
}
