<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * JSON View class for the SermonSpeaker Component
 *
 * @since  5.5
 */
class SermonspeakerViewSermons extends JViewLegacy
{
	/**
	 * Creates the JSON data.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise a Error object.
	 * @throws \Exception
	*/
	public function display($tpl = null)
	{
		// Get some data from the models
		$state = $this->get('State');
		$items = $this->get('Items');

		// Get Category stuff from models
		$category = $this->get('Category');
		$children = $this->get('Children');
		$parent   = $this->get('Parent');
		$children = array($category->id => $children);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->setHeader('status',  500 . ' ' . implode("\n", $errors));
			echo new JResponseJson(null, implode("\n", $errors), true);

			return;
		}

		if ($category == false)
		{
			JFactory::getApplication()->setHeader('status',  404 . ' ' .  JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			echo new JResponseJson(null, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), true);

			return;
		}

		if ($parent == false && $category->id != 'root')
		{
			JFactory::getApplication()->setHeader('status',  404 . ' ' .  JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			echo new JResponseJson(null, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), true);

			return;
		}

		// Check whether category access level allows access.
		$user   = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		if (in_array($category->access, $groups))
		{
			JFactory::getApplication()->setHeader('status',  403 . ' ' .  JText::_('JERROR_ALERTNOAUTHOR'));
			echo new JResponseJson(null, JText::_('JERROR_ALERTNOAUTHOR'), true);

			return;
		}

		foreach ($items as $item)
		{

		}

		echo new JResponseJson($items);
	}
}
