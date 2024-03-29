<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\ListField;

defined('_JEXEC') or die();

/**
 * Fullorderingsermons field class for SermonSpeaker.
 * Used in the sermons list
 *
 * @package  SermonSpeaker
 * @since    5.3
 */
class JFormFieldFullorderingsermons extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Fullorderingsermons';

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form
	 *                                      field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for
	 *                                      the field. For example if the field has name="foo" and the group value is
	 *                                      set to "bar" then the full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		// Set the default value from params.
		if (!$value)
		{
			$params   = ComponentHelper::getParams('com_sermonspeaker');
			$order    = $params->get('default_order', 'ordering');
			$orderDir = $params->get('default_order_dir', 'asc');
			$value    = 'sermons.' . $order . ' ' . $orderDir;
		}

		return parent::setup($element, $value, $group);
	}
}
