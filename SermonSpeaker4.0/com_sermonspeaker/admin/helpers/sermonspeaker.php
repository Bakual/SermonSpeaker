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
			JText::_('COM_SERMONSPEAKER_MENU_STATISTICS'),
			'index.php?option=com_sermonspeaker&view=statistics&format=raw',
			$vName == 'statistics'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_SERMONSPEAKER_MENU_HELP'),
			'index.php?option=com_sermonspeaker&view=help',
			$vName == 'help'
		);
	}
	
	/**
	 * Get the actions for ACL
	 */
	public static function getActions($categoryId = 0)
	{
		$user  	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_sermonspeaker';
		} else {
			$assetName = 'com_sermonspeaker.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}