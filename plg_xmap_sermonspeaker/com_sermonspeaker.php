<?php

/**
 * @license GNU/GPL
 * @description Xmap plugin for SermonSpeaker component
 */
defined('_JEXEC') or die('Restricted access.');

class xmap_com_sermonspeaker
{
	/*
	 * This function is called before a menu item is printed. We use it to set the
	 * proper uniqueid for the item and indicate whether the node is expandible or not
	 */
	static function prepareMenuItem($node, &$params) {
		$uri	= JURI::getInstance($node->link);
		$view	= $uri->getVar('view');
		switch($view){
			case 'sermon':
				$id = (int)$uri->getVar('id', 0);
				if ($id) {
					$node->uid = 'com_sermonspeaker_sermon_'.$id;
					$node->expandible = false;
				}
				break;
			case 'sermons':
				$node->uid = 'com_sermonspeaker_sermons';
				$node->expandible = true;
				break;
			case 'serie':
				$id = (int)$uri->getVar('id', 0);
				$node->uid = 'com_sermonspeaker_serie_'.$id;
				$node->expandible = true;
				break;
			case 'series':
			case 'seriessermon':
				$node->uid = 'com_sermonspeaker_series';
				$node->expandible = true;
				break;
			case 'speaker':
				$id = (int)$uri->getVar('id', 0);
				$node->uid = 'com_sermonspeaker_speaker_'.$id;
				$node->expandible = true;
				break;
			case 'speakers':
				$node->uid = 'com_sermonspeaker_speakers';
				$node->expandible = true;
				break;
		}
	}

	static function getTree($xmap, $parent, &$params) {
		$uri	= JURI::getInstance($parent->link);
		$view	= $uri->getVar('view');

		// Nothing to expand
		if ($view == 'sermon'){
			return;
		}

		// Calculate Params
		$priority	= JArrayHelper::getValue($params, 'serie_priority', $parent->priority, '');
		$changefreq	= JArrayHelper::getValue($params, 'serie_changefreq', $parent->changefreq, '');
		$params['serie_priority']	= ($priority == '-1') ? $parent->priority : $priority;
		$params['serie_changefreq']	= ($changefreq == '-1') ? $parent->changefreq : $changefreq;

		$priority	= JArrayHelper::getValue($params, 'speaker_priority', $parent->priority, '');
		$changefreq	= JArrayHelper::getValue($params, 'speaker_changefreq', $parent->changefreq, '');
		$params['speaker_priority']	= ($priority == '-1') ? $parent->priority : $priority;
		$params['speaker_changefreq']	= ($changefreq == '-1') ? $parent->changefreq : $changefreq;

		$priority	= JArrayHelper::getValue($params, 'sermon_priority', $parent->priority, '');
		$changefreq	= JArrayHelper::getValue($params, 'sermon_changefreq', $parent->changefreq, '');
		$params['sermon_priority']	= ($priority == '-1') ? $parent->priority : $priority;
		$params['sermon_changefreq']	= ($changefreq == '-1') ? $parent->changefreq : $changefreq;

		switch($view){
			case 'sermons':
				xmap_com_sermonspeaker::getSermonsTree($xmap, $parent, $params, $view);
				break;
			case 'serie':
			case 'speaker':
				$id	= (int)$uri->getVar('id', 0);
				xmap_com_sermonspeaker::getSermonsTree($xmap, $parent, $params, $view, $id);
				break;
			case 'series':
				xmap_com_sermonspeaker::getSeriesTree($xmap, $parent, $params, $view);
				break;
			case 'speakers':
				xmap_com_sermonspeaker::getSpeakersTree($xmap, $parent, $params);
				break;
		}
	}

	static function getSermonsTree($xmap, $parent, $params, $view, $id = 0) {
		require_once JPATH_SITE.'/components/com_sermonspeaker/models/sermons.php';
		$model	= new SermonspeakerModelSermons();
		$state	= $model->getState();
		$state->set('list.limit', $params['limit']);
		$state->set('list.start', 0);
		$state->set('list.ordering', 'ordering');
		$state->set('list.direction', 'ASC');
		if($view == 'serie' && $id){
			$state->set('serie.id', $id);
		} elseif($view == 'speaker' && $id) {
			$state->set('speaker.id', $id);
		}
		$items	= $model->getItems();
		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node		= new stdclass;
			$node->id	= $parent->id;
			$node->uid	= $parent->uid.'_sermon_'.$item->id;
			$node->name	= htmlspecialchars($item->title);
			$node->link	= 'index.php?option=com_sermonspeaker&view=sermon&id='.$item->slug.'&Itemid='.$parent->id;
			$node->priority		= $params['sermon_priority'];
			$node->changefreq	= $params['sermon_changefreq'];
			$node->modified		= ($item->created != '0000-00-00 00:00:00') ? strtotime($item->created) : strtotime($item->sermon_date);
			$node->expandible	= false;
			$xmap->printNode($node);
		}
		$xmap->changeLevel(-1);
	}

	static function getSeriesTree($xmap, $parent, $params, $view, $id = 0) {
		require_once JPATH_SITE.'/components/com_sermonspeaker/models/series.php';
		$model	= new SermonspeakerModelSeries();
		$state	= $model->getState();
		$state->set('list.limit', $params['limit']);
		$state->set('list.start', 0);
		$state->set('list.ordering', 'ordering');
		$state->set('list.direction', 'ASC');
		if($id){
			$state->set('speaker.id', $id);
		}
		$items	= $model->getItems();
		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node		= new stdclass;
			$node->id	= $parent->id;
			$node->uid	= $parent->uid.'_serie_'.$item->id;
			$node->name	= htmlspecialchars($item->title);
			$node->link	= 'index.php?option=com_sermonspeaker&view=serie&id='.$item->slug.'&Itemid='.$parent->id;
			$node->priority		= $params['serie_priority'];
			$node->changefreq	= $params['serie_changefreq'];
			if ($item->created != '0000-00-00 00:00:00'){
				$node->modified = strtotime($item->created);
			}
			$node->expandible	= true;
			if($xmap->printNode($node) && $params['serie_expand']){
				self::getSermonsTree($xmap, $parent, $params, 'serie', $item->id);
			}
		}
		$xmap->changeLevel(-1);
	}

	static function getSpeakersTree($xmap, $parent, $params) {
		require_once JPATH_SITE.'/components/com_sermonspeaker/models/speakers.php';
		$model	= new SermonspeakerModelSpeakers();
		$state	= $model->getState();
		$state->set('list.limit', $params['limit']);
		$state->set('list.start', 0);
		$state->set('list.ordering', 'ordering');
		$state->set('list.direction', 'ASC');
		$items	= $model->getItems();
		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node		= new stdclass;
			$node->id	= $parent->id;
			$node->uid	= $parent->uid.'_speaker_'.$item->id;
			$node->name	= htmlspecialchars($item->title);
			$node->link	= 'index.php?option=com_sermonspeaker&view=speaker&id='.$item->slug.'&Itemid='.$parent->id;
			$node->priority		= $params['speaker_priority'];
			$node->changefreq	= $params['speaker_changefreq'];
			if ($item->created != '0000-00-00 00:00:00'){
				$node->modified = strtotime($item->created);
			}
			$node->expandible	= true;
			if($xmap->printNode($node) && $params['speaker_expand']){
				self::getSermonsTree($xmap, $parent, $params, 'speaker', $item->id);
			}
		}
		$xmap->changeLevel(-1);
	}
}