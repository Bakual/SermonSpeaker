<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

/**
 * Helper class for Related Sermons module
 *
 * @since  1.0
 */
class ModRelatedSermonsHelper
{
	private static $option;

	private static $view;

	private static $id;

	/**
	 * @var  JDatabaseDriver
	 */
	private static $db;

	/**
	 * Gets the items from the database
	 *
	 * @param   object  $params  parameters
	 *
	 * @return  array  $related  Array of items
	 */
	public static function getList($params)
	{
		$jinput   = Factory::getApplication()->input;
		self::$id = $jinput->getInt('id');

		if (!self::$id)
		{
			return array();
		}

		self::$option = $jinput->getCmd('option');
		self::$view   = $jinput->getCmd('view');

		// Get Params
		$supportContent = $params->get('supportArticles', 0);
		$limitSermons   = $params->get('limitSermons', 10);
		$orderBy        = $params->get('orderBy', 'CreatedDateDesc');
		$sermonCat      = $params->get('sermon_cat', 0);

		$related = array();

		if (($supportContent && self::$option == 'com_content' && self::$view == 'article')
			|| (self::$option == 'com_sermonspeaker' && self::$view == 'sermon'))
		{
			$keywords = self::getKeywords();

			if ($keywords)
			{
				$related = self::getRelatedSermonsById($keywords, $orderBy, $sermonCat, $limitSermons);

				if ($supportContent && $limitSermons > count($related))
				{
					$articles = self::getRelatedItemsById($keywords, $limitSermons - count($related));
					$related = array_merge($related, $articles);
				}
			}
		}

		return $related;
	}

	/**
	 * Get keywords from current item, either com_content or com_sermonspeaker
	 *
	 * @return  array  $keywords  Array of items
	 */
	private static function getKeywords()
	{
		self::$db = Factory::getDbo();
		$query    = self::$db->getQuery(true);

		$query->select('metakey');
		$query->where('id = ' . self::$id);

		if (self::$option == 'com_content')
		{
			$query->from('#__content');
		}
		else
		{
			$query->from('#__sermon_sermons');
		}

		self::$db->setQuery($query);
		$metakey	= self::$db->loadResult();
		$keys		= explode(',', $metakey);
		$keywords	= array();

		foreach ($keys as $key)
		{
			$key = trim($key);

			if ($key)
			{
				$keywords[] = $key;
			}
		}

		foreach ($keywords as &$keyword)
		{
			$keyword = self::$db->escape($keyword);
		}

		return $keywords;
	}

	/**
	 * Search the sermons
	 *
	 * @param   array   $keywords      Keywords
	 * @param   string  $orderBy       Ordering
	 * @param   int     $sermonCat     Category
	 * @param   int     $limitSermons  Limit
	 *
	 * @return  array  $related  Array of items
	 */
	protected static function getRelatedSermonsById($keywords, $orderBy, $sermonCat, $limitSermons)
	{
		$related = array();

		switch ($orderBy)
		{
			case 'NameAsc':
				$SermonOrder = 'a.title ASC';
				break;
			case 'NameDes':
				$SermonOrder = 'a.title DESC';
				break;
			case 'SermonDateAsc':
				$SermonOrder = 'a.sermon_date ASC';
				break;
			case 'SermonDateDes':
				$SermonOrder = 'a.sermon_date DESC';
				break;
			case 'CreatedDateAsc':
				$SermonOrder = 'a.created ASC';
				break;
			default:
				$SermonOrder = 'a.created DESC';
				break;
		}

		$query = self::$db->getQuery(true);
		$query->select('a.title, a.created, a.catid, a.language');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__sermon_sermons AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');

		if (self::$option == 'com_sermonspeaker')
		{
			$query->where('a.id != ' . self::$id);
		}

		$query->where('a.state = 1');
		$query->where('(a.catid = 0 OR cc.published = 1)');

		// Define null and now dates
		$nullDate = self::$db->quote(self::$db->getNullDate());
		$nowDate  = self::$db->quote(Factory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		if ($sermonCat)
		{
			$query->where('a.catid = ' . $sermonCat);
		}

		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'
			. implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords) . ',%")');
		$app = Factory::getApplication();

		if ($app->getLanguageFilter())
		{
			$query->where('a.language in (' . self::$db->quote(Factory::getLanguage()->getTag()) . ',' . self::$db->quote('*') . ')');
		}

		$query->group('a.id');
		$query->order($SermonOrder);

		self::$db->setQuery($query, 0, $limitSermons);
		$temp = self::$db->loadObjectList();

		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/route.php';

		foreach ($temp as $row)
		{
			$row->route = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug, $row->catid, $row->language));
			$related[] = $row;
		}

		return $related;
	}

	/**
	 * Search articles
	 *
	 * @param   array  $keywords  Keywords
	 * @param   int    $limit     Limit
	 *
	 * @return  array  $related  Array of items
	 */
	private static function getRelatedItemsById($keywords, $limit)
	{
		$user		= Factory::getUser();
		$groups		= implode(',', $user->getAuthorisedViewLevels());

		$nullDate	= self::$db->getNullDate();
		$date		= Factory::getDate();
		$now		= $date->toSql();

		$related 	= array();

		$query	= self::$db->getQuery(true);
		$query->select('a.title, a.created');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__content AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');

		if (self::$option == 'com_content')
		{
			$query->where('a.id != ' . self::$id);
		}

		$query->where('a.state = 1');
		$query->where('a.access IN (' . $groups . ')');
		$query->where('cc.published = 1');
		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'
			. implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords) . ',%")');
		$query->where('(a.publish_up = ' . self::$db->quote($nullDate) . ' OR a.publish_up <= ' . self::$db->quote($now) . ')');
		$query->where('(a.publish_down = ' . self::$db->quote($nullDate) . ' OR a.publish_down >= ' . self::$db->quote($now) . ')');

		// Filter by language
		$app = Factory::getApplication();

		if ($app->getLanguageFilter())
		{
			$query->where('a.language in (' . self::$db->quote(Factory::getLanguage()->getTag()) . ',' . self::$db->quote('*') . ')');
		}

		self::$db->setQuery($query, 0, $limit);
		$temp = self::$db->loadObjectList();

		require_once JPATH_SITE . '/components/com_content/helpers/route.php';

		foreach ($temp as $row)
		{
			$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
			$related[] = $row;
		}

		return $related;
	}
}
