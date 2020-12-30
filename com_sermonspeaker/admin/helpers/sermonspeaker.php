<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * Sermonspeaker Helper
 *
 * @since  3.4
 */
class SermonspeakerHelper
{
	/**
	 * Get the actions for ACL
	 *
	 * @since ?
	 *
	 * @param int $categoryId
	 *
	 * @return \JObject
	 */
	public static function getActions($categoryId = 0)
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		if (empty($categoryId))
		{
			$assetName = 'com_sermonspeaker';
		}
		else
		{
			$assetName = 'com_sermonspeaker.category.' . (int) $categoryId;
		}

		$actions = JAccess::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/access.xml'
		);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns valid contexts
	 *
	 * @return  array
	 *
	 * @since   5.6.0
	 */
	public static function getContexts()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_sermonspeaker', JPATH_ADMINISTRATOR)
		|| $lang->load('com_sermonspeaker', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker');

		return array(
			'com_sermonspeaker.sermon'              => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERMON'),
			'com_sermonspeaker.serie'               => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERIE'),
			'com_sermonspeaker.speaker'             => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SPEAKER'),
			'com_sermonspeaker.sermons.categories'  => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SERMONS'),
			'com_sermonspeaker.series.categories'   => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SERIES'),
			'com_sermonspeaker.speakers.categories' => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SPEAKERS'),
		);
	}

	/**
	 * Map the section for custom fields.
	 *
	 * @param   string $section The section to get the mapping for
	 *
	 * @return  string  The new section
	 *
	 * @since  5.6.0
	 */
	public static function validateSection($section)
	{
		$mapping = array(
			'sermon'         => 'sermon',
			'sermons'        => 'sermon',
			'frontendupload' => 'sermon',
			'intro'          => 'speaker',
			'bio'            => 'speaker',
			'speaker'        => 'speaker',
			'speakers'       => 'speaker',
			'speakerform'    => 'speaker',
			'serie'          => 'serie',
			'series'         => 'serie',
			'serieform'      => 'serie',
		);

		return isset($mapping[$section]) ? $mapping[$section] : null;
	}
}
