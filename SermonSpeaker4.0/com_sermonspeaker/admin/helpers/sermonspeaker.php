<?php

// No direct access
defined('_JEXEC') or die;

class SermonspeakerHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'main')
	{
		// Switch Layout if in Joomla 3.0
		$version	= new JVersion;
		$joomla30	= $version->isCompatible(3.0);
		if ($joomla30)
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
				JText::_('COM_SERMONSPEAKER_MENU_TAGS'),
				'index.php?option=com_sermonspeaker&view=tags',
				$vName == 'tags'
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
		else
		{
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_SERIES'),
				'index.php?option=com_sermonspeaker&view=series',
				$vName == 'series'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_SPEAKERS'),
				'index.php?option=com_sermonspeaker&view=speakers',
				$vName == 'speakers'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_SERMONS'),
				'index.php?option=com_sermonspeaker&view=sermons',
				$vName == 'sermons'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_TAGS'),
				'index.php?option=com_sermonspeaker&view=tags',
				$vName == 'tags'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_CATEGORY'),
				'index.php?option=com_categories&extension=com_sermonspeaker',
				$vName == 'categories'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_TOOLS'),
				'index.php?option=com_sermonspeaker&view=tools',
				$vName == 'tools'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_LANGUAGES'),
				'index.php?option=com_sermonspeaker&view=languages',
				$vName == 'languages'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_SERMONSPEAKER_MENU_HELP'),
				'index.php?option=com_sermonspeaker&view=help',
				$vName == 'help'
			);
		}
	}
	
	/**
	 * Get the actions for ACL
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId))
		{
			$assetName = 'com_sermonspeaker';
		}
		else
		{
			$assetName = 'com_sermonspeaker.category.'.(int) $categoryId;
		}

		$actions = JAccess::getActions('com_sermonspeaker', 'component');
		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}