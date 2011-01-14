<?php
/**
* @copyright	Copyright (C) 2010. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* This module is based on the mod_related_items from Joomla Core
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

abstract class modRelatedSermonsHelper
{
	public static function getList($params)
	{
		$this->db		= JFactory::getDbo();
		$this->app		= JFactory::getApplication();
		$user			= JFactory::getUser();
		$groups			= implode(',', $user->getAuthorisedViewLevels());
		$date			= JFactory::getDate();

		$this->option	= JRequest::getCmd('option');
		$view			= JRequest::getCmd('view');

		$this->id		= JRequest::getInt('id');

		$nullDate		= $this->db->getNullDate();
		$this->now		= $date->toMySQL();
		$related		= array();

		$supportArticles = $params->get('supportArticles', 0);
		$this->ss_itemid = (int)$params->get('menuitem');
		
		$related = array();
		if (($supportArticles && $this->option == 'com_content' && $view == 'article') || ($this->option == 'com_sermonspeaker' && $view == 'sermon') && $this->id)
		{
			$related = modRelatedSermonsHelper::getRelatedSermonsById();
			if ($supportArticles){
				$articles = modRelatedSermonsHelper::getRelatedItemsById();
				$related = array_merge($related, $articles);
			}
		}
		
		return $related;
	}

	protected function getRelatedItemsById() {
		$query		= $this->db->getQuery(true);
		$related 	= array();

		if ($option == 'com_content'){
			// select the meta keywords from the article
			$query->select('metakey');
			$query->from('#__content');
			$query->where('id = '.$this->id);
			$this->db->setQuery($query);
		} elseif ($option == 'com_sermonspeaker'){
			// select the meta keywords from the sermon
			$query->select('metakey');
			$query->from('#__sermon_sermons');
			$query->where('id = '.$this->id);
			$this->db->setQuery($query);
		}

		if ($metakey = trim($this->db->loadResult())) {
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key) {
				$key = trim($key);
				if ($key) {
					$likes[] = ','.$this->db->getEscaped($key).','; // surround with commas so first and last items have surrounding commas
				}
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
				$query->where('a.id != '.$this->id);
				$query->where('a.state = 1');
				$query->where('a.access IN ('.$groups.')');
				$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%', $likes).'%")'); //remove single space after commas in keywords)
				$query->where('(a.publish_up = '.$this->db->Quote($nullDate).' OR a.publish_up <= '.$this->db->Quote($now).')');
				$query->where('(a.publish_down = '.$this->db->Quote($nullDate).' OR a.publish_down >= '.$this->db->Quote($now).')');

				// Filter by language
				if ($app->getLanguageFilter()) {
					$query->where('a.language in ('.$this->db->Quote(JFactory::getLanguage()->getTag()).','.$this->db->Quote('*').')');
				}

				$this->db->setQuery($query);
				$temp = $this->db->loadObjectList();

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
	protected function getRelatedSermonsById() {
		$query		= $this->db->getQuery(true);
		$related 	= array();

		if ($option == 'com_content'){
			// select the meta keywords from the article
			$query->select('metakey');
			$query->from('#__content');
			$query->where('id = '.$this->id);
			$this->db->setQuery($query);
		} elseif ($option == 'com_sermonspeaker'){
			// select the meta keywords from the sermon
			$query->select('metakey');
			$query->from('#__sermon_sermons');
			$query->where('id = '.$this->id);
			$this->db->setQuery($query);
		}

		if ($metakey = trim($this->db->loadResult())) {
			// explode the meta keys on a comma
			$keys = explode(',', $metakey);
			$likes = array ();

			// assemble any non-blank word(s)
			foreach ($keys as $key) {
				$key = trim($key);
				if ($key) {
					$likes[] = ','.$this->db->getEscaped($key).','; // surround with commas so first and last items have surrounding commas
				}
			}

			if (count($likes)) {
				// select other items based on the metakey field 'like' the keys found
				$query->clear();
				$query = 'SELECT id, sermon_title AS title, sermon_date AS created,' .
						' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(":", id, alias) ELSE id END as slug'.
						' FROM #__sermon_sermons' .
						' WHERE id != '.(int) $this->id .
						' AND published = 1' .
						' AND ( CONCAT(",", REPLACE(metakey,", ",","),",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(metakey,", ",","),",") LIKE "%', $likes).'%" )'; //remove single space after commas in keywords
				$this->db->setQuery($query);
				$query->select('a.id');
				$query->select('a.sermon_title AS a.title');
				$query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
				$query->select('a.catid');
				$query->select('cc.access AS cat_access');
				$query->select('cc.published AS cat_state');
				$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
				$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
				$query->from('#__sermon_sermons AS a');
				$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
				$query->where('a.id != '.$this->id);
				$query->where('a.state = 1');
				$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%'.implode('%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%', $likes).'%")'); //remove single space after commas in keywords)

				$this->db->setQuery($query);
				$temp = $this->db->loadObjectList();

				if (count($temp)) {
					foreach ($temp as $row) {
						if ($row->cat_state == 1) {
						
							// TODO: include Route Helper
							// $row->route = JRoute::_(SermonspeakerHelperRoute::getSermonRoute($row->slug));
							$row->route = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$this->ss_itemid);
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
