<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die;

class modRelatedSermonsHelper
{
	private static $option;
	private static $view;
	private static $id;
	private static $db;

	public static function getList($params)
	{
		self::$id		= JRequest::getInt('id');
		if (!self::$id)
		{
			return array();
		}
		self::$option	= JRequest::getCmd('option');
		self::$view		= JRequest::getCmd('view');

		// Get Params
		$supportContent	= $params->get('supportArticles', 0);
		$limitSermons	= $params->get('limitSermons', 10);
		$orderBy		= $params->get('orderBy', 'CreatedDateDesc');
		$sermonCat		= $params->get('sermon_cat', 0);

		$related = array();
		if (($supportContent && self::$option == 'com_content' && self::$view == 'article') || (self::$option == 'com_sermonspeaker' && self::$view == 'sermon'))
		{
			$keywords	= self::getKeywords();
			if ($keywords)
			{
				$related = self::getRelatedSermonsById($keywords, $orderBy, $sermonCat, $limitSermons);
				if ($supportContent && $limitSermons > count($related))
				{
					$articles = self::getRelatedItemsById($keywords, $orderBy, $limitSermons - count($related));
					$related = array_merge($related, $articles);
				}
			}
		}

		return $related;
	}

	/**
	* Get keywords from current item, either com_content or com_sermonspeaker
	*/
	private static function getKeywords()
	{
		self::$db	= JFactory::getDbo();
		$query		= self::$db->getQuery(true);

		$query->select('metakey');
		$query->where('id = '.self::$id);
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

		if (self::$option == 'com_sermonspeaker')
		{
			$query		= self::$db->getQuery(true);
			$query->select('tags.title');
			$query->from('#__sermon_sermons_tags AS st');
			$query->where('st.sermon_id = '.self::$id);
			$query->join('LEFT', '#__sermon_tags AS tags ON st.tag_id = tags.id');

			self::$db->setQuery($query);
			$tags = self::$db->loadResultArray();
			if ($tags)
			{
				$keywords	= array_merge($keywords, $tags);
			}
		}

		foreach ($keywords as &$keyword)
		{
			$keyword = self::$db->escape($keyword);
		}

		return $keywords;
	}

	// Search the sermons
	protected static function getRelatedSermonsById($keywords, $orderBy, $sermonCat, $limitSermons)
	{
		$query		= self::$db->getQuery(true);
		$related	= array();

		switch ($orderBy) 
		{
			case 'NameAsc':
				$SermonOrder = 'a.sermon_title ASC';
				break;
			case 'NameDes':
				$SermonOrder = 'a.sermon_title DESC';
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

		$query	= self::$db->getQuery(true);
		$query->select('a.sermon_title AS title, a.created');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__sermon_sermons AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
		$query->leftJoin('#__sermon_sermons_tags AS st ON st.sermon_id = a.id');
		$query->leftJoin('#__sermon_tags AS tags ON tags.id = st.tag_id');
		if(self::$option == 'com_sermonspeaker')
		{
			$query->where('a.id != '.self::$id);
		}
		$query->where('a.state = 1');
		$query->where('(a.catid = 0 OR cc.published = 1)');
		if($sermonCat)
		{
			$query->where('a.catid = '.$sermonCat);
		}
		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'.implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords).',%" OR tags.title IN ("'.implode('","', $keywords).'"))');
		$app = JFactory::getApplication();
		if ($app->getLanguageFilter())
		{
			$query->where('a.language in ('.self::$db->Quote(JFactory::getLanguage()->getTag()).','.self::$db->Quote('*').')');
		}
		$query->group('a.id');
		$query->order($SermonOrder);

		self::$db->setQuery($query, 0, $limitSermons);
		$temp = self::$db->loadObjectList();

		require_once (JPATH_SITE.'/components/com_sermonspeaker/helpers/route.php');
		foreach ($temp as $row)
		{
			$row->route = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
			$related[] = $row;
		}

		return $related;
	}

	// Search articles
	private static function getRelatedItemsById($keywords, $limit)
	{
		$user			= JFactory::getUser();
		$groups			= implode(',', $user->getAuthorisedViewLevels());

		$nullDate		= self::$db->getNullDate();
		$date			= JFactory::getDate();
		$now			= $date->toSql();

		$related 	= array();

		$query	= self::$db->getQuery(true);
		$query->select('a.title, a.created');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__content AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
		if(self::$option == 'com_content')
		{
			$query->where('a.id != '.self::$id);
		}
		$query->where('a.state = 1');
		$query->where('a.access IN ('.$groups.')');
		$query->where('cc.published = 1');
		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'.implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords).',%")');
		$query->where('(a.publish_up = '.self::$db->Quote($nullDate).' OR a.publish_up <= '.self::$db->Quote($now).')');
		$query->where('(a.publish_down = '.self::$db->Quote($nullDate).' OR a.publish_down >= '.self::$db->Quote($now).')');
		// Filter by language
		$app = JFactory::getApplication();
		if ($app->getLanguageFilter())
		{
			$query->where('a.language in ('.self::$db->Quote(JFactory::getLanguage()->getTag()).','.self::$db->Quote('*').')');
		}
		self::$db->setQuery($query, 0, $limit);
		$temp = self::$db->loadObjectList();

		require_once (JPATH_SITE.'/components/com_content/helpers/route.php');
		foreach ($temp as $row)
		{
			$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
			$related[] = $row;
		}

		return $related;
	}
}