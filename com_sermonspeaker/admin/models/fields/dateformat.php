<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Dateformat Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldDateformat extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Dateformat';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
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

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();
		$date	 = JHtml::Date('', 'Y-m-d H:m:s', true);
		$dateformats = array('DATE_FORMAT_LC', 'DATE_FORMAT_LC1', 'DATE_FORMAT_LC2', 'DATE_FORMAT_LC3', 'DATE_FORMAT_LC4');
		foreach ($dateformats AS $key => $format){
			$options[$key]['value']	 = $format;
			$options[$key]['text']	 = JHtml::Date($date, JText::_($format), true);
		}

		return $options;
	}
}
