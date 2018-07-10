<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Autotweet
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Categories\Categories;

defined('_JEXEC') or die();


/**
 * SermonSpeaker integration plugin for SocialBacklinks from JoomUnited - Adapter
 *
 * @since 1.0.0
 */
class PlgSBSermonspeakerAdapter extends SBPluginsContent
{
	protected $_map = array(
		'items_table'      => array('__table' => '#__sermonspeaker_sermons'),
		'categories_table' => array('__table' => '#__categories', 'title' => 'title'),
	);

	/**
	 * @see   SBAdaptersPlugin::getAlias()
	 *
	 * @since 1.0.0
	 */
	public function getAlias()
	{
		return 'sermonspeaker';
	}

	/**
	 * @see   SBAdaptersPluginsContentsInterface::getNewItemsConditions()
	 *
	 * @since 1.0.0
	 */
	public function getNewItemsConditions($settings)
	{
		$where   = parent::getNewItemsConditions($settings);
		$where[] = 'tbl.`state` = 1';

		return $where;
	}

	/**
	 * @see   SBAdaptersPluginsContentsInterface::getItemRoute()
	 *
	 * @since 1.0.0
	 */
	public function getItemRoute($item)
	{
		JLoader::register('K2HelperRoute', JPATH_ROOT . '/components/com_k2/helpers/route.php');
		$slug = empty($item->alias) ? $item->id : $item->id . ':' . $item->alias;

		if ($item->catid !== 0)
		{
			$categoryNode = Categories::get($item->catid);

			$catslug = empty($categoryNode->alias) ? $categoryNode->id : $categoryNode->id . ':' . $categoryNode->alias;
		}

		return K2HelperRoute::getItemRoute($slug, $catslug);
	}

	/**
	 * @see   SBPluginsContentsInterface::getTreeOfCategories()
	 *
	 * @since 1.0.0
	 */
	public function getTreeOfCategories()
	{
		$cats  = Categories::getInstance('com_sermonspeaker');

		$categories = array();

		foreach ($cats as $cat)
		{
			$categories[] = array(
				'_type'        => 'category',
				'title'        => $cat->title,
				'id'           => $cat->id,
				'parent_id'    => $cat->parent_id,
				'_hasChildren' => false,
				'_children'    => array(),
			);
		}

		$root = array(
			'_type'        => 'category',
			'title'        => 'Select a category',
			'id'           => 0,
			'parent_id'    => null,
			'_hasChildren' => false,
			'_children'    => array(),
		);
		$this->assignChildren($root, $categories);

		$result[] = $root;

		return $result;
	}

	/**
	 * Recursive function that uses pointers to get the Tree
	 *
	 * @since 1.0.0
	 */
	public function assignChildren(&$item, &$categories)
	{
		if ($item['_hasChildren'])
			return;

		$item['_hasChildren'] = true;
		foreach ($categories as &$category)
		{
			if ($category['parent_id'] == $item['id'])
			{
				$item['_children'][] = &$category;
				$this->assignChildren($category, $categories);
			}
		}
	}

}
