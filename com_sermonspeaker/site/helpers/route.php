<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Utilities\ArrayHelper;

/**
 * Sermonspeaker Component Route Helper
 *
 * @since  4.0
 */
abstract class SermonspeakerHelperRoute
{
	protected static $lookup = array();

	protected static $langs;

	/**
	 * Get Sermons Route
	 *
	 * @param   int         $catid     Category ID of the sermons
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSermonsRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'sermons' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermons';

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_sermons'),
				'sermons'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path               = array_reverse($category->getPath());
				$needles['sermons'] = ArrayHelper::toInteger($path);
			}

			$link .= '&catid=' . (int) $catid;
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get Sermon Route
	 *
	 * @param   int         $id        ID of the sermon
	 * @param   int         $catid     Category ID of the sermon
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSermonRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'sermon'  => array((int) $id),
			'sermons' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=sermon&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_sermons'),
				'sermons'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path               = array_reverse($category->getPath());
				$needles['sermons'] = ArrayHelper::toInteger($path);
			}
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get Series Route
	 *
	 * @param   int         $catid     Category ID of the series
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSeriesRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'series' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=series';

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_series'),
				'series'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path              = array_reverse($category->getPath());
				$needles['series'] = ArrayHelper::toInteger($path);
			}

			$link .= '&catid=' . (int) $catid;
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get Serie Route
	 *
	 * @param   int         $id        ID of the serie
	 * @param   int         $catid     Category ID of the serie
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSerieRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'serie'  => array((int) $id),
			'series' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=serie&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_series'),
				'series'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path              = array_reverse($category->getPath());
				$needles['series'] = ArrayHelper::toInteger($path);
			}
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get Speakers Route
	 *
	 * @param   int         $catid     Category ID of the speakers
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSpeakersRoute($catid = 0, $language = 0)
	{
		$needles = array(
			'speakers' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speakers';

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_speakers'),
				'speakers'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path                = array_reverse($category->getPath());
				$needles['speakers'] = ArrayHelper::toInteger($path);
			}

			$link .= '&catid=' . (int) $catid;
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get Speaker Route
	 *
	 * @param   int         $id        ID of the speaker
	 * @param   int         $catid     Category ID of the speaker
	 * @param   int|string  $language  Language tag
	 *
	 * @return string URL
	 *
	 * @since ?
	 */
	public static function getSpeakerRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'speaker'  => array((int) $id),
			'speakers' => array((int) $catid),
		);

		// Create the link
		$link = 'index.php?option=com_sermonspeaker&view=speaker&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = Factory::getApplication()->bootComponent('sermonspeaker')->getCategory(
				array('table' => '#__sermon_speakers'),
				'speakers'
			);
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path                = array_reverse($category->getPath());
				$needles['speakers'] = ArrayHelper::toInteger($path);
			}
		}

		if ($language && $language != "*" && Multilanguage::isEnabled())
		{
			if (!isset(self::$langs))
			{
				self::_getLanguages();
			}

			foreach (self::$langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link                .= '&lang=' . $lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Find Items
	 *
	 * @param   array  $needles  Array of properties to search
	 *
	 * @return int ID of the menu item
	 *
	 * @since ?
	 */
	protected static function _findItem($needles = null)
	{
		$app      = Factory::getApplication();
		$menus    = $app->getMenu('site');
		$active   = $menus->getActive();
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component = ComponentHelper::getComponent('com_sermonspeaker');

			$attributes = array('component_id');
			$values     = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[]     = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);

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
							self::$lookup[$language][$view][$item->query['id']] = array();
						}

						self::$lookup[$language][$view][$item->query['id']][] = $item->id;
					}
					else
					{
						$catid = $item->getParams()->get('catid', 0);

						if (!isset(self::$lookup[$language][$view][$catid]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$catid] = array();
						}

						self::$lookup[$language][$view][$catid][] = $item->id;
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
					foreach ($ids as $id)
					{
						if ($id)
						{
							if (isset(self::$lookup[$language][$view][(int) $id]))
							{
								// Check if active menuitem is part of the valid options
								if ($active && in_array($active->id, self::$lookup[$language][$view][(int) $id]))
								{
									return $active->id;
								}

								return self::$lookup[$language][$view][(int) $id][0];
							}
						}
						else
						{
							// $id is 0 in case no category is specified by the calling layout
							if (isset(self::$lookup[$language][$view]))
							{
								return reset(self::$lookup[$language][$view])[0];
							}
						}
					}
				}
			}
		}

		if ($active && $active->component == 'com_sermonspeaker'
			&& ($active->language == '*' || $language == '*' || $active->language == $language
				|| !Multilanguage::isEnabled())
		)
		{
			return $active->id;
		}

		// Get first SermonSpeaker sermons list menuitem found
		if (isset(self::$lookup[$language]['sermons']))
		{
			$sermonslist = reset(self::$lookup[$language]['sermons'])[0];

			if ($sermonslist)
			{
				return $sermonslist;
			}
		}

		// Get first SermonSpeaker menuitem found
		if (isset(self::$lookup[$language]))
		{
			$first = reset(self::$lookup[$language]);

			while (is_array($first))
			{
				$first = reset($first);
			}

			if ($first)
			{
				return $first;
			}
		}

		// If nothing found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}

	/**
	 * Get the category route. Proxy for category finder plugin
	 *
	 * @param   integer  $catid     The category ID.
	 * @param   integer  $language  The language code.
	 * @param   string   $layout    The layout value.
	 *
	 * @return  string  The sermons route.
	 *
	 * @since   1.5
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.extension')
			->from('#__categories AS a')
			->where('`id` = ' . $catid);

		$db->setQuery($query);
		$extension = $db->loadResult();

		$parts  = explode('.', $extension);
		$method = 'get' . $parts[1] . 'Route';

		return self::$method($catid, $language);
	}

	/**
	 * Stores languages
	 *
	 * @return void
	 *
	 * @since ?
	 */
	protected static function _getLanguages()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('a.sef AS sef')
			->select('a.lang_code AS lang_code')
			->from('#__languages AS a');

		$db->setQuery($query);
		self::$langs = $db->loadObjectList();
	}
}
