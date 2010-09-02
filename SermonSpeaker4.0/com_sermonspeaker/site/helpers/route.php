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

	public static function getSermonsRoute($id)
	{
		$needles = array(
			'sermons'  => array((int) $id) // needle = view
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermons&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) { // Check if there is a menu entry for this link
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSermonRoute($id)
	{
		$needles = array(
			'sermon'  => array((int) $id) // needle = view
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermon&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) { // Check if there is a menu entry for this link
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSeriesRoute($id)
	{
		$needles = array(
			'series'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=series&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSerieRoute($id)
	{
		$needles = array(
			'serie'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=serie&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSpeakersRoute($id)
	{
		$needles = array(
			'speakers'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speakers&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	public static function getSpeakerRoute($id)
	{
		$needles = array(
			'speaker'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speaker&id='. $id;

		if ($item = SermonspeakerHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		};

		return $link;
	}

	protected static function _findItem($needles) // searches for an existing menu entry for a given view and id
	{
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_sermonspeaker');
			$menus		= JApplication::getMenu('site');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{ // compile an array $lookup[view][id] = ItemID
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}
		foreach ($needles as $view => $ids)
		{ // search $lookup for $view and $id and return ItemID if found
			if (isset(self::$lookup[$view]))
			{
				foreach($ids as $id)
				{
					if (isset(self::$lookup[$view][(int)$id])) {
						return self::$lookup[$view][(int)$id];
					}
				}
			}
		}

		return null;
	}
}
