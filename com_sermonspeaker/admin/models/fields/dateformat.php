<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

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
		// Load languages and merge with fallbacks
		$jlang = JFactory::getLanguage();
		$jlang->load('com_sermonspeaker', JPATH_ADMINISTRATOR.'/components/com_sermonspeaker', 'en-GB', true);
		$jlang->load('com_sermonspeaker', JPATH_ADMINISTRATOR.'/components/com_sermonspeaker', null, true);

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
