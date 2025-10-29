<?php
/**
 * @package     Joomla.Site
 * @subpackage  MOD_SERMONSPEAKER_categories
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Bakual\Module\SermonspeakerCategories\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;

/**
 * Helper for MOD_SERMONSPEAKER_categories
 *
 * @since  1.5
 */
abstract class SermonspeakerCategoriesHelper
{
	/**
	 * Get list of Items
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function getList($params)
	{
		$type = $params->get('parent_type', 'sermons');
		$options               = [];
		$options['countItems'] = $params->get('numitems', 0);

		$categories = Categories::getInstance('Sermonspeaker.' . $type, $options);
		$category   = $categories->get($params->get('parent', 'root'));

		if ($category !== null)
		{
			$items = $category->getChildren();

			$count = $params->get('count', 0);

			if ($count > 0 && \count($items) > $count)
			{
				$items = \array_slice($items, 0, $count);
			}

			return $items;
		}

		return array();
	}
}
