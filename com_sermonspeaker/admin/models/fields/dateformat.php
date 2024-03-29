<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Dateformat Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class JFormFieldDateformat extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Dateformat';

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @since    1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options     = array();
		$date        = HTMLHelper::date('', 'Y-m-d H:m:s', true);
		$dateformats = array('DATE_FORMAT_LC', 'DATE_FORMAT_LC1', 'DATE_FORMAT_LC2', 'DATE_FORMAT_LC3', 'DATE_FORMAT_LC4');
		foreach ($dateformats as $key => $format)
		{
			$options[$key]['value'] = $format;
			$options[$key]['text']  = HTMLHelper::date($date, Text::_($format), true);
		}

		return $options;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{
		// Check for old 'path' setting and apply it to 'path_audio' and 'path_video'. B/C for versions < 5.0.3
		if ($path = $this->form->getValue('path'))
		{
			$this->form->setValue('path_audio', null, $path);
			$this->form->setValue('path_video', null, $path);
			$this->form->setValue('path', null, '');
		}

		return parent::getInput();
	}
}
