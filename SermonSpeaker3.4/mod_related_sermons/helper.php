<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modRelatedSermonsHelper
{
	function getList($params)
	{
		global $mainframe;

		$db					=& JFactory::getDBO();
		$user =& JFactory::getUser();

		$option				= JRequest::getCmd('option');
		$view				= JRequest::getCmd('view');

		$temp				= JRequest::getString('id');
		$temp				= explode(':', $temp);
		$id					= $temp[0];

		$aid = $user->get('aid', 0);

		$showDate			= $params->get('showDate', 0);
		$supportArticles		= $params->get('supportArticles', 0);
		$conf =& JFactory::getConfig();
		
		$related = array();
		if (($supportArticles && $option == 'com_content' && $view == 'article') || ($option == 'com_sermonspeaker' && $view == 'sermon') && $id)
		{
			if ($params->get('cache_items', 0)==1 && $conf->getValue( 'config.caching' )) {
				$cache =& JFactory::getCache('mod_related_items', 'callback');
				$cache->setLifeTime( $params->get( 'cache_time', $conf->getValue( 'config.cachetime' ) * 60 ) );
				$cache->setCacheValidation(true);
				$related = $cache->get(array('modRelatedSermonsHelper', 'getRelatedSermonsById'), array($id, $aid, $showDate, $option));
				if ($supportArticles){
					$articles = $cache->get(array('modRelatedSermonsHelper', 'getRelatedItemsById'), array($id, $aid, $showDate, $option));
					$related = array_merge($related, $articles);
				}
			} else {
				$related = modRelatedSermonsHelper::getRelatedSermonsById($id, $aid, $showDate, $option);
				if ($supportArticles){
					$articles = modRelatedSermonsHelper::getRelatedItemsById($id, $aid, $showDate, $option);
					$related = array_merge($related, $articles);
				}
			}
		}
		
		return $related;
	}

	function getRelatedItemsById($id, $aid, $showDate, $option) {
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$date =& JFactory::getDate();

		$related = array();

		$nullDate = $db->getNullDate();
		$now  = $date->toMySQL();

		// select the meta keywords from the item
		if ($option == 'com_content'){
			$query = 'SELECT metakey' .
					' FROM #__content' .
					' WHERE id = '.(int) $id;
		} elseif ($option == 'com_sermonspeaker'){
			$query = 'SELECT metakey' .
					' FROM #__sermon_sermons' .
					' WHERE id = '.(int) $id;
		}
		$db->setQuery($query);

		if ($metakey = trim($db->loadResult()))
		{
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key)
			{
				$key = trim($key);
				if ($key) {
					$likes[] = ',' . $db->getEscaped($key) . ','; // surround with commas so first and last items have surrounding commas
				}
			}

			if (count($likes))
			{
				// select other items based on the metakey field 'like' the keys found
				$query = 'SELECT a.id, a.title, DATE_FORMAT(a.created, "%Y-%m-%d") AS created, a.sectionid, a.catid, cc.access AS cat_access, s.access AS sec_access, cc.published AS cat_state, s.published AS sec_state,' .
						' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
						' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
						' FROM #__content AS a' .
						' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' .
						' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
						' LEFT JOIN #__sections AS s ON s.id = a.sectionid' .
						' WHERE a.id != '.(int) $id .
						' AND a.state = 1' .
						' AND a.access <= ' .(int) $user->get('aid', 0) .
						' AND ( CONCAT(",", REPLACE(a.metakey,", ",","),",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(a.metakey,", ",","),",") LIKE "%', $likes).'%" )' . //remove single space after commas in keywords
						' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )' .
						' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )';
				$db->setQuery($query);
				$temp = $db->loadObjectList();

				if (count($temp))
				{
					foreach ($temp as $row)
					{
						if (($row->cat_state == 1 || $row->cat_state == '') && ($row->sec_state == 1 || $row->sec_state == '') && ($row->cat_access <= $user->get('aid', 0) || $row->cat_access == '') && ($row->sec_access <= $user->get('aid', 0) || $row->sec_access == ''))
						{
							$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
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
	function getRelatedSermonsById($id, $aid, $showDate, $option) {
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$date =& JFactory::getDate();

		$related = array();

		$nullDate = $db->getNullDate();
		$now  = $date->toMySQL();

		// select the meta keywords from the item
		if ($option == 'com_content'){
			$query = 'SELECT metakey' .
					' FROM #__content' .
					' WHERE id = '.(int) $id;
		} elseif ($option == 'com_sermonspeaker'){
			$query = 'SELECT metakey' .
					' FROM #__sermon_sermons' .
					' WHERE id = '.(int) $id;
		}
		$db->setQuery($query);

		if ($metakey = trim($db->loadResult()))
		{
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key)
			{
				$key = trim($key);
				if ($key) {
					$likes[] = ',' . $db->getEscaped($key) . ','; // surround with commas so first and last items have surrounding commas
				}
			}
			if (count($likes))
			{
				// select other items based on the metakey field 'like' the keys found
				$query = 'SELECT id, sermon_title AS title, sermon_date AS created,' .
						' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(":", id, alias) ELSE id END as slug'.
						' FROM #__sermon_sermons' .
						' WHERE id != '.(int) $id .
						' AND published = 1' .
						' AND ( CONCAT(",", REPLACE(metakey,", ",","),",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(metakey,", ",","),",") LIKE "%', $likes).'%" )'; //remove single space after commas in keywords
				$db->setQuery($query);
				$temp = $db->loadObjectList();

				if (count($temp))
				{
					foreach ($temp as $row)
					{
						$row->route = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug);
						$related[] = $row;
					}
				}
				unset ($temp);
			}
		}

		return $related;
	}
}
