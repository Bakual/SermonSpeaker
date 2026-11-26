<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Filter;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFilterInterface;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

/**
 * Form Filter class for the MediamanagerfolderField
 *
 * @since  7.1.4
 */
class MediamanagerfolderFilter implements FormFilterInterface
{
	/**
	 * Method to filter a field value.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   ?Registry          $input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   ?Form              $form     The form object for which the field is being tested.
	 *
	 * @return  mixed   The filtered value.
	 *
	 * @since   7.1.4
	 */
	public function filter(\SimpleXMLElement $element, $value, $group = null, ?Registry $input = null, ?Form $form = null)
	{
		if (empty($value))
		{
			return false;
		}

		if (is_array($value))
		{
			$value = implode('', $value);
		}

		return $value;
	}
}