<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Sermonspeaker Component Route Helper
 *
 * @static
 * @package		Sermonspeaker
 * @since 4.0
 */
abstract class SermonspeakerHelperRoute
{
	protected static $lookup;

	public static function getSermonsRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'sermons' => array(0)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermons';
		if ($catid){
			$link .= '&catid='.$catid;
		}

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSermonRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'sermon' => array((int)$id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermon&id='.$id;

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem(array('sermons'=>array(0)))) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSeriesRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'series' => array(0)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=series';
		if ($catid){
			$link .= '&catid='.$catid;
		}

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSerieRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'serie'  => array((int)$id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=serie&id='.$id;
		if ((int)$catid > 1) {
			$categories = JCategories::getInstance('Sermonspeaker', array('table'=>'#__sermon_series'));
			$category 	= $categories->get((int)$catid);
			if($category) {
				$needles['category']	= array_reverse($category->getPath());
				$needles['categories'] 	= $needles['category'];
				$link 	.= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem(array('series'=>array(0)))) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSpeakersRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'speakers' => array(0)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speakers';
		if ($catid)
		{
			$link .= '&catid='.$catid;
		}
		if ($item = SermonspeakerHelperRoute::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSpeakerRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'speaker'  => array((int)$id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speaker&id='.$id;
		if ((int)$catid > 1) {
			$categories = JCategories::getInstance('Sermonspeaker', array('table'=>'#__sermon_speakers'));
			$category 	= $categories->get((int)$catid);
			if($category) {
				$needles['category']	= array_reverse($category->getPath());
				$needles['categories'] 	= $needles['category'];
				$link 	.= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem(array('speakers'=>array(0)))) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_sermonspeaker');
			$items		= $menus->getItems('component_id', $component->id);
			if ($items){ // Populate static $lookup with SermonSpeaker menu entries: $lookup[view][id]
				foreach ($items as $item) {
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];
						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}
						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						} else {
							self::$lookup[$view][] = $item->id;
						}
					}
				}
			}
		}
		if ($needles) {
			foreach ($needles as $view => $ids) { // Search $lookup for matching menu entry
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id) {
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		} else { // Check if active menu entry is from SermonSpeaker
			$active = $menus->getActive();
			if ($active && $active->component == 'com_sermonspeaker') {
				return $active->id;
			}
		}

		return null;
	}
}
