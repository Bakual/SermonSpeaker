<?php
/**
 * @package     Joomla.Site
 * @subpackage  MOD_SERMONSPEAKER_categories
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sermonspeaker\Module\SermonspeakerCategories\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\Categories;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

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
	 * @param \Joomla\Registry\Registry  &$params module parameters
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function getCategories(Registry $params, SiteApplication $app): array
	{
		$type = $params->get('parent_type', 'sermons');
		$options               = [];
		$options['countItems'] = $params->get('numitems', 0);

		$categories = $app->bootComponent('com_categories')->getCategory($options, $type);
//		$categories = Categories::getInstance('Sermonspeaker.' . $type, $options);
		$category   = $categories->get($params->get('parent', 'root'));

		if ($category !== null)
		{
			$items = $category->getChildren();

			$count = $params->get('count', 0);

			if ($count > 0 && count($items) > $count)
			{
				$items = array_slice($items, 0, $count);
			}

			return $items;
		}

		return array();
	}
}
