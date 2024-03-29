<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Administrator.Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models', 'SermonspeakerModel');

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
	 * @param   /Joomla/Registry/Registry  $params  The module parameters.
	 *
	 * @return  array array of items or false
	 *
	 * @since   ?
	 */
	public static function getList($params, $type)
	{
		if (!in_array($type, array('sermons', 'series', 'speakers')))
		{
			return array();
		}

		// Get an instance of the SermonSpeaker item model
		$model = BaseDatabaseModel::getInstance($type, 'SermonspeakerModel', array('ignore_request' => true));

		// Set States
		$model->setState('list.select',
			$type . '.id, ' . $type . '.catid, ' . $type . '.title, ' . $type . '.checked_out, '
			. $type . '.checked_out_time, ' . $type . '.created, ' . $type . '.created_by, ' . $type . '.hits');

		$ordering = $params->get('ordering', 'hits');

		if (!in_array($ordering, array('hits', 'title', 'created', 'modified')))
		{
			$ordering = 'hits';
		}

		$model->setState('list.ordering', $type . '.' . $ordering);
		$model->setState('list.direction', $params->get('direction') ? 'ASC' : 'DESC');

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 2));

		try
		{
			$items = $model->getItems();
		}
		catch (RuntimeException $e)
		{
			return array();
		}

		// Authenticate and create the links
		$user = Factory::getuser();
		$view = rtrim($type, 's');

		foreach ($items as $item)
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
