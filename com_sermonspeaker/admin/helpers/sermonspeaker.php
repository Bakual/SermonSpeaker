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

		if (JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_sermonspeaker')->get('custom_fields_enable', '1'))
		{
			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELDS'),
				'index.php?option=com_fields&context=com_sermonspeaker.sermon',
				$vName == 'fields.sermon'
			);
			JHtmlSidebar::addEntry(
				JText::_('JGLOBAL_FIELD_GROUPS'),
				'index.php?option=com_categories&extension=com_sermonspeaker.sermon.fields',
				$vName == 'categories.sermon'
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
			JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/access.xml',
			"/access/section[@name='component']/"
		);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
