<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Helper class for SermonSpeaker module
 *
 * @since  1.0
 */
abstract class ModSermonspeakerHelper
{
	/**
	 * Gets the items from the database
	 *
	 * @param   object  $params  parameters
	 *
	 * @return  array  $items  Array of items
	 */
	public static function getList($params)
	{
		// Collect params
		$mode   = (int) $params->get('mode');
		$cat_id = (int) $params->get('cat');
		$sort   = (int) $params->get('sort');

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		if ($mode == 2)
		{
			$items = array();

			/** @var $categories JCategories */
			$categories = JCategories::getInstance('Sermonspeaker');
			$root       = $categories->get($cat_id);

			if ($root->hasChildren())
			{
				$categories = $root->getChildren(true);

				foreach ($categories as $category)
				{
					/** @var $category JCategoryNode */
					$item = new stdclass;

					/** @var $params Joomla\Registry\Registry */
					$params        = $category->getParams();
					$item->id      = $category->id;
					$item->slug    = $category->slug;
					$item->title   = $category->title;
					$item->tooltip = $category->description;
					$item->level   = $category->level;
					$item->pic     = $params->get('image');
					$items[]       = $item;
				}
			}
		}
		else
		{
			if ($mode)
			{
				$query->select('id, title, series_description as tooltip, avatar as pic');
				$query->select('CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
				$query->from('#__sermon_series');

				if ($sort)
				{
					$query->order('ordering ASC');
				}
				else
				{
					$query->order('title ASC');
				}
			}
			else
			{
				$query->select('id, title, intro as tooltip, pic, CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug, 1 as level');
				$query->from('#__sermon_speakers');

				if ($sort)
				{
					$query->order('ordering ASC');
				}
				else
				{
					$query->order('title ASC');
				}
			}

			$query->where('state = 1');

			// Define null and now dates
			$nullDate = $db->quote($db->getNullDate());
			$nowDate  = $db->quote(JFactory::getDate()->toSql());

			// Filter by start and end dates.
			$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')');
			$query->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');

			if ($cat_id)
			{
				$query->where('catid = ' . $cat_id);
			}

			$db->setQuery($query, 0, (int) $params->get('limit', 0));
			$items = $db->loadObjectList();
		}

		return $items;
	}
}
