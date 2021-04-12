<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.form.formfield');
FormHelper::loadFieldClass('number');

/**
 * Hits Field class for the SermonSpeaker
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class JFormFieldHits extends JFormFieldNumber
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Hits';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{
		$html = '<div class="input-group">';
		$html .= parent::getInput();

		if ($this->value)
		{
			$html .= '<button class="btn btn-primary"
							type="button"
							onclick="document.getElementById(\'' . $this->id . '\').value=\'0\'"
							title="' . Text::_('JSEARCH_RESET') . '">
							<span class="icon-loop" aria-hidden="true"></span>
							<span class="visually-hidden">' . Text::_('JSEARCH_RESET') . '</span>
						</button>';
		}

		$html .= '</div>';

		return $html;
	}
}
