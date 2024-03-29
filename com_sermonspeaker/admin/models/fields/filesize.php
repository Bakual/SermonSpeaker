<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Form\Field\TextField;

defined('_JEXEC') or die();

/**
 * Filesize Field class for the SermonSpeaker
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class JFormFieldFilesize extends TextField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Filesize';

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

		require_once(JPATH_COMPONENT_SITE . '/helpers/sermonspeaker.php');
		$title = SermonspeakerHelperSermonspeaker::convertBytes($this->value, true, false);

		$html .= '<button class="btn btn-info"
						type="button"
						title="' . $title . '">
						<span class="icon-help" aria-hidden="true"></span>
						<span class="visually-hidden">' . $title . '</span>
					</button>';
		$html .= '</div>';

		return $html;
	}
}
