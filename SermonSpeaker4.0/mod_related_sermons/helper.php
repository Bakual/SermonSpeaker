<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_SITE.'/components/com_content/helpers/route.php');

abstract class modRelatedSermonsHelper
{
	public static function getList($params)
	{
		$db				= JFactory::getDbo();

		$option			= JRequest::getCmd('option');
		$view			= JRequest::getCmd('view');

		$id				= JRequest::getInt('id');

		$related		= array();

		$supportArticles = $params->get('supportArticles', 0);
		$limitSermons	 = $params->get('limitSermons', 10);
		$orderBy		 = $params->get('orderBy');
		$sermonCat		 = $params->get('sermon_cat');
		
		$ss_itemid 		 = (int)$params->get('menuitem');

		$related = array();
		if (($supportArticles && $option == 'com_content' && $view == 'article') || ($option == 'com_sermonspeaker' && $view == 'sermon') && $id)
		{
			$related = self::getRelatedSermonsById($db, $option, $id, $ss_itemid, $limitSermons, $orderBy, $sermonCat );
			if ($supportArticles){
				$articles = self::getRelatedItemsById($db, $option, $id, $limitSermons, $orderBy, $sermonCat);
				$related = array_merge($related, $articles);
			}
		}
		
		return $related;
	}

	protected static function getRelatedItemsById($db, $option, $id, $limitSermons, $orderBy, $sermonCat) {
		$user			= JFactory::getUser();
		$groups			= implode(',', $user->getAuthorisedViewLevels());

		$nullDate		= $db->getNullDate();
		$date			= JFactory::getDate();
		$now			= $date->toMySQL();

		$query		= $db->getQuery(true);
		$related 	= array();

		if ($option == 'com_content'){
			// select the meta keywords from the article
			$query->select('metakey');
			$query->from('#__content');
			$query->where('id = '.$id);
			$db->setQuery($query);
		} elseif ($option == 'com_sermonspeaker'){
			// select the meta keywords from the sermon
			$query->select('metakey');
			$query->from('#__sermon_sermons');
			$query->where('id = '.$id);
			$db->setQuery($query);
		}

		if ($metakey = trim($db->loadResult())) {
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key) {
				$key = trim($key);
				if ($key) {
					$likes[] = ','.$db->escape($key).','; // surround with commas so first and last items have surrounding commas
				}
			}
			switch ($orderBy) 
			{
				case "NameAsc":
					$SermonOrder = " ORDER BY a.sermon_title ASC";
					break;
				case "NameDes":
					$SermonOrder = " ORDER BY a.sermon_title DESC";
					break;
				case "SermonDateAsc":
					$SermonOrder = " ORDER BY a.sermon_date ASC";
					break;
				case "SermonDateDes":
					$SermonOrder = " ORDER BY a.sermon_date DESC";
					break;
				case "CreatedDateAsc":
					$SermonOrder = " ORDER BY a.created ASC";
					break;
				default:
					$SermonOrder = " ORDER BY a.created DESC";
					break;
			}
			
			if (count($likes)) {
				// select other items based on the metakey field 'like' the keys found
				$query->clear();
				$query->select('a.id');
				$query->select('a.title');
				$query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
				$query->select('a.catid');
				$query->select('cc.access AS cat_access');
				$query->select('cc.published AS cat_state');
				$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
				$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
				$query->from('#__content AS a');
				$query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
				$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
				$query->where('a.id != '.$id);
				$query->where('a.state = 1');
				if($sermonCat != 0)
				{
					$query->where('a.catid = '.$sermonCat);
				}			
				$query->where('a.access IN ('.$groups.')');
				$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%', $likes).'%")'); //remove single space after commas in keywords)
				$query->where('(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')');
				$query->where('(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).') '.$SermonOrder);
				// Filter by language
				$app = JFactory::getApplication();
				if ($app->getLanguageFilter()) {
					$query->where('a.language in ('.$db->Quote(JFactory::getLanguage()->getTag()).','.$db->Quote('*').')');
				}
				$db->setQuery($query, 0 ,$limitSermons);
				$temp = $db->loadObjectList();

				if (count($temp)) {
					foreach ($temp as $row) {
						if ($row->cat_state == 1) {
							$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
							$related[] = $row;
						}
					}
				}
				unset ($temp);
			}
		}

		return $related;
	}
	
	// Search the sermons
	protected static function getRelatedSermonsById($db, $option, $id, $ss_itemid, $limitSermons, $orderBy, $sermonCat ) {
		$query		= $db->getQuery(true);
		$related 	= array();

		if ($option == 'com_content')
		{
			// select the meta keywords from the article
			$query->select('metakey');
			$query->from('#__content');
			$query->where('id = '.$id);
			$db->setQuery($query);
		} 
		elseif ($option == 'com_sermonspeaker')
		{
			// select the meta keywords from the sermon
			$query->select('metakey');
			$query->from('#__sermon_sermons');
			$query->where('id = '.$id);
			$db->setQuery($query);
		}

		if ($metakey = trim($db->loadResult())) 
		{
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key) 
			{
				$key = trim($key);
				if ($key) 
				{
					$likes[] = ','.$db->escape($key).','; // surround with commas so first and last items have surrounding commas
				}
			}

			switch ($orderBy) 
			{
				case "NameAsc":
					$SermonOrder = " ORDER BY a.sermon_title ASC";
					break;
				case "NameDes":
					$SermonOrder = " ORDER BY a.sermon_title DESC";
					break;
				case "SermonDateAsc":
					$SermonOrder = " ORDER BY a.sermon_date ASC";
					break;
				case "SermonDateDes":
					$SermonOrder = " ORDER BY a.sermon_date DESC";
					break;
				case "CreatedDateAsc":
					$SermonOrder = " ORDER BY a.created ASC";
					break;
				default:
					$SermonOrder = " ORDER BY a.created DESC";
					break;
			}

			if (count($likes)) {
				// select other items based on the metakey field 'like' the keys found
				$query->clear();
				$db->setQuery($query);
				$query->select('a.id');
				$query->select('a.sermon_title AS title');
				$query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
				$query->select('a.catid');
				$query->select('cc.access AS cat_access');
				$query->select('cc.published AS cat_state');
				$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
				$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
				$query->from('#__sermon_sermons AS a');
				$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
				$query->where('a.id != '.$id);
				$query->where('a.state = 1');
				if($sermonCat != 0)
				{
					$query->where('a.catid = '.$sermonCat);
				}
				$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%', $likes).'%") '.$SermonOrder); //remove single space after commas in keywords)
				$db->setQuery($query, 0 , $limitSermons);
				$temp = $db->loadObjectList();

				if (count($temp)) {
					foreach ($temp as $row) {
						if ($row->catid == 0){
							$row->cat_state = 1;
						}
						if ($row->cat_state == 1) {
							// TODO: include Route Helper
							// $row->route = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
							$row->route = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$ss_itemid);
							$related[] = $row;
						}
					}
				}
				unset ($temp);
			}
		}

		return $related;
	}
}
