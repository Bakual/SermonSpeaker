<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
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
	 * Configure the Linkbar.
	 *
	 * @param  string $vName The name of the active view.
	 *
	 * @since  1.6
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = 'main')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_SERIES'),
			'index.php?option=com_sermonspeaker&view=series',
			$vName == 'series'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_SPEAKERS'),
			'index.php?option=com_sermonspeaker&view=speakers',
			$vName == 'speakers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_SERMONS'),
			'index.php?option=com_sermonspeaker&view=sermons',
			$vName == 'sermons'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_CATEGORY'),
			'index.php?option=com_categories&extension=com_sermonspeaker',
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_TOOLS'),
			'index.php?option=com_sermonspeaker&view=tools',
			$vName == 'tools'
		);

		if (JComponentHelper::isEnabled('com_fields'))
		{
			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELD_GROUPS'),
				'index.php?option=com_fields&view=groups&context=com_content.article',
				$vName == 'fields.groups'
			);
			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELD_GROUPS'),
				'index.php?option=com_fields&view=groups&context=com_sermonspeaker.sermon',
				$vName == 'fields.groups'
			);
		}

		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_LANGUAGES'),
			'index.php?option=com_sermonspeaker&view=languages',
			$vName == 'languages'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_HELP'),
			'index.php?option=com_sermonspeaker&view=help',
			$vName == 'help'
		);
	}

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
		$user = JFactory::getUser();
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
			JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/access.xml',
			"/access/section[@name='component']/"
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

		$contexts = array(
			'com_sermonspeaker.sermon'  => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERMON'),
			'com_sermonspeaker.serie'   => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERIE'),
			'com_sermonspeaker.speaker' => JText::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SPEAKER'),
		);

		return $contexts;
	}

	/**
	 * Map the section for custom fields.
	 *
	 * @param   string  $section  The section to get the mapping for
	 *
	 * @return  string  The new section
	 *
	 * @since  5.6.0
	 */
	public static function validateSection($section)
	{
		$mapping = array(
			'sermon'         => 'sermon',
			'frontendupload' => 'sermon',
			'speaker'        => 'speaker',
			'speakerform'    => 'speaker',
			'serie'          => 'serie',
			'serieform'      => 'serie',
		);

		return isset($mapping[$section]) ? $mapping[$section] : null;
	}
}
