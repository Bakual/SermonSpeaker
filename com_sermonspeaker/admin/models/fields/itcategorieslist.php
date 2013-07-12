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
 * Itcategorieslist Field class for the SermonSpeaker.
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldItcategorieslist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Itcategorieslist';

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
		$options[0]['value']	= 0;
		$options[0]['text']		= JText::_('JNONE');

		// List of iTunes Categories (from http://www.apple.com/itunes/podcasts/specs.html#categories)
		$cats['Arts']				= array('Design', 'Fashion & Beauty', 'Food', 'Literature', 'Performing Arts', 'Visual Arts');
		$cats['Business']			= array('Business News', 'Careers', 'Investing', 'Management & Marketing', 'Shopping');
		$cats['Comedy']				= array();
		$cats['Education']			= array('Education', 'Education Technology', 'Higher Education', 'K-12', 'Language Courses', 'Training');
		$cats['Games & Hobbies']	= array('Automotive', 'Aviation', 'Hobbies', 'Other Games', 'Video Games');
		$cats['Government & Organizations']	= array('Local', 'National', 'Non-Profit', 'Regional');
		$cats['Health']				= array('Alternative Health', 'Fitness & Nutrition', 'Self-Help', 'Sexuality');
		$cats['Kids & Family']		= array();
		$cats['Music']				= array();
		$cats['News & Politics']	= array();
		$cats['Religion & Spirituality']	= array('Buddhism', 'Christianity', 'Hinduism', 'Islam', 'Judaism', 'Other', 'Spirituality');
		$cats['Science & Medicine']	= array('Medicine', 'Natural Sciences', 'Social Sciences');
		$cats['Society & Culture']	= array('History', 'Personal Journals', 'Philosophy', 'Places & Travel');
		$cats['Sports & Recreation']	= array('Amateur', 'College & High School', 'Outdoor', 'Professional');
		$cats['Technology']			= array('Gadgets', 'Tech News', 'Podcasting', 'Software How-To');
		$cats['TV & Film']			= array();

		$i = 0;
		foreach ($cats AS $key => $value)
		{
			$i++;
			$options[$i]['value']	= $key;
			$text					= JText::_('COM_SERMONSPEAKER_ITCAT_'.strtoupper(str_replace(array(' ', '&'), '-', $key)));
			$options[$i]['text']	= $text;
			if ($value)
			{
				foreach ($value AS $subvalue)
				{
					$i++;
					$options[$i]['value']	= $key.' > '.$subvalue;
					$subtext				= JText::_('COM_SERMONSPEAKER_ITCAT_'.strtoupper(str_replace(array(' ', '&'), '-', $key.'--'.$subvalue)));
					$options[$i]['text']	= $text.' > '.$subtext;
				}
			}
		}

		return $options;
	}
}
