<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

/**
 * Supports an HTML select list of ordering
 * Copied from com_weblink
 *
 * @package        Sermonspeaker.Administrator
 *
 * @since          ?
 */
class SermonorderingField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Sermonordering';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput(): string
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . $this->element['class'] . '"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . $this->element['onchange'] . '"' : '';

		// Get some field values from the form.
		$Id         = (int) $this->form->getValue('id');
		$categoryId = (int) $this->form->getValue('catid');

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, ' . $this->element['field'] . ' AS text' .
			' FROM #__' . $this->element['table'] .
			' WHERE catid = ' . $categoryId .
			' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = HTMLHelper::_('list.ordering', '', $query, trim($attr), $this->value, $Id ? 0 : 1);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = HTMLHelper::_('list.ordering', $this->name, $query, trim($attr), $this->value, $Id ? 0 : 1);
		}

		return implode($html);
	}
}