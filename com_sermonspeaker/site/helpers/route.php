<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Sermonspeaker Component Route Helper
 *
 * @static
 * @package		Sermonspeaker
 * @since 4.0
 */
abstract class SermonspeakerHelperRoute
{
	protected static $lookup = array();
	protected static $langs;

	public static function getSermonsRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'sermons' => array((int)$catid)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermons';
		if ($catid){
			$link .= '&catid='.$catid;
		}

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSermonRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'sermon' => array((int)$id),
			'sermons' => array((int)$catid)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermon&id='.$id;

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSeriesRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'series' => array((int)$catid)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=series';
		if ($catid){
			$link .= '&catid='.$catid;
		}

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSerieRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'serie'  => array((int)$id),
			'series'  => array((int)$catid)
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

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSpeakersRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'speakers' => array((int)$catid)
		);
		//Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speakers';
		if ($catid)
		{
			$link .= '&catid='.$catid;
		}

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getSpeakerRoute($id, $catid = 0, $language = 0)
	{

		$needles = array(
			'speaker'  => array((int)$id),
			'speakers'  => array((int)$catid)
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

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}
			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_sermonspeaker');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items		= $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id']))
					{
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
					else
					{
						$catid	= $item->params->get('catid', 0);
						if (!isset(self::$lookup[$language][$view][$catid]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$catid] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}

		// Check for an active SermonSpeaker menuitem
		$active = $menus->getActive();
		if ($active && $active->component == 'com_sermonspeaker' && ($active->language == '*' || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// Get first SermonSpeaker menuitem found
		if (isset(self::$lookup[$language]))
		{
			$first	= reset(self::$lookup[$language]);
			if ($first)
			{
				return reset($first);
			}
		}

		// if nothing found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	}

	protected static function _getLanguages()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('a.sef AS sef')
			->select('a.lang_code AS lang_code')
			->from('#__languages AS a');

		$db->setQuery($query);
		self::$langs = $db->loadObjectList();

		return;
	}

}
