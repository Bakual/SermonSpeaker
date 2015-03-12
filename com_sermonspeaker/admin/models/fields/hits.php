<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

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
			$html	.= '<i'.$onclick.' class="btn add-on icon-loop" rel="tooltip" title="'.JText::_('JSEARCH_RESET').'"> </i>';
		}
		$html	.= '</div>';

		return $html;
	}
}
