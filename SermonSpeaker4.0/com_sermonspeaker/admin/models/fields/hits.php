<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');

/**
 * Hits Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldHits extends JFormFieldText
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

		$html	= '<div class="input-append">';
		$html	.= parent::getInput();
		if ($this->value){
			$version	= new JVersion;
			$joomla30	= $version->isCompatible(3.0);
			if ($joomla30)
			{
				$html	.= '<i'.$onclick.' class="btn add-on icon-loop" rel="tooltip" title="'.JText::_('JSEARCH_RESET').'"> </i>';
			}
			else
			{
				$html	.= '<img src="'.JURI::base().'components/com_sermonspeaker/images/reset.png"'.$onclick.' class="pointer" width="16" height="16" border="0" title="'.JText::_('JSEARCH_RESET').'" alt="Reset" />';
			}
		}
		$html .= '</div>';

		return $html;
	}
}
