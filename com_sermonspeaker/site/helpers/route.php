<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

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
	 * @param   int    $catid    Category ID of the sermons
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_sermons'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path               = array_reverse($category->getPath());
				$needles['sermons'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   int    $id       ID of the sermon
	 * @param   int    $catid    Category ID of the sermon
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_sermons'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path               = array_reverse($category->getPath());
				$needles['sermons'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   int    $catid    Category ID of the series
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_series'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path              = array_reverse($category->getPath());
				$needles['series'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   int    $id       ID of the serie
	 * @param   int    $catid    Category ID of the serie
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_series'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path              = array_reverse($category->getPath());
				$needles['series'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   int    $catid    Category ID of the speakers
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_speakers'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path                = array_reverse($category->getPath());
				$needles['speakers'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   int    $id       ID of the speaker
	 * @param   int    $catid    Category ID of the speaker
	 * @param   string $language Language tag
	 *
	 * @return string URL
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
			$categories = JCategories::getInstance('Sermonspeaker', array('table' => '#__sermon_speakers'));
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$path                = array_reverse($category->getPath());
				$needles['speakers'] = \Joomla\Utilities\ArrayHelper::toInteger($path);
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
					$link .= '&lang=' . $lang->sef;
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
	 * @param   array $needles Array of properties to search
	 *
	 * @return int ID of the menu item
	 */
	protected static function _findItem($needles = null)
	{
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component = JComponentHelper::getComponent('com_sermonspeaker');

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
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
					else
					{
						$catid = $item->params->get('catid', 0);

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
					foreach ($ids as $id)
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
			$first = reset(self::$lookup[$language]);

			if ($first)
			{
				return reset($first);
			}
		}

		// If nothing found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}

	/**
	 * Stores languages
	 *
	 * @return void
	 */
	protected static function _getLanguages()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.sef AS sef')
			->select('a.lang_code AS lang_code')
			->from('#__languages AS a');

		$db->setQuery($query);
		self::$langs = $db->loadObjectList();

		return;
	}
}
