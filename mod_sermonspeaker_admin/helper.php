<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Administrator.Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models', 'SermonspeakerModel');

/**
 * Helper for mod_sermonspeaker
 *
 * @since  1.0
 */
class ModSermonspeakerHelper
{
	/**
	 * Get a list of SermonSpeaker items
	 *
	 * @param   Joomla /Registry/Registry  $params  The module parameters.
	 *
	 * @return  array of items or false
	 */
	public static function getList($params, $type)
	{
		if (!in_array($type, array('sermons', 'series', 'speakers')))
		{
			return false;
		}

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance($type, 'SermonspeakerModel', array('ignore_request' => true));

		// Set States
		$model->setState('list.select',
			$type . '.id, ' . $type . '.catid, ' . $type . '.title, ' . $type . '.checked_out, '
			. $type . '.checked_out_time, ' . $type . '.created, ' . $type . '.hits');

		$ordering = $params->get('ordering', 'hits');

		if (!in_array($ordering, array('hits', 'title', 'created', 'modified')))
		{
			$ordering = 'hits';
		}

		$model->setState('list.ordering', $type . '.' . $ordering);
		$model->setState('list.direction', $params->get('direction') ? 'ASC' : 'DESC');

		// Set Category Filter
		$categoryId = $params->get('catid');

		if (is_numeric($categoryId))
		{
			$model->setState('filter.category_id', $categoryId);
		}

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

		try
		{
			$items = $model->getItems();
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		// Authenticate and create the links
		$user = JFactory::getuser();
		$view = rtrim($type, 's');

		foreach ($items as &$item)
		{
			if ($user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid))
			{
				$item->link = 'index.php?option=com_sermonspeaker&task=' . $view . '.edit&id=' . $item->id;
			}
			else
			{
				$item->link = '';
			}
		}

		return $items;
	}
}
