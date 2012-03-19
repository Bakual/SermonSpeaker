<?php

/**
 * @license GNU/GPL
 * @description Xmap plugin for Joomla's web links component
 */
defined('_JEXEC') or die('Restricted access.');

class xmap_com_sermonspeaker
{
	static private $_initialized = false;

	/*
	 * This function is called before a menu item is printed. We use it to set the
	 * proper uniqueid for the item and indicate whether the node is expandible or not
	 */
	static function prepareMenuItem($node, &$params)
	{
		$uri	= JURI::getInstance($node->link);
		$view	= $uri->getVar('view');
		switch($view){
			case 'sermon':
				$id = (int)$uri->getVar('id', 0);
				if ($id) {
					$node->uid = 'com_sermonspeakersermon'.$id;
					$node->expandible = false;
				}
				break;
			case 'sermons':
				$node->uid = 'com_sermonspeakersermons';
				$node->expandible = true;
				break;
			case 'serie':
				$id = (int)$uri->getVar('id', 0);
				$node->uid = 'com_sermonspeakerserie'.$id;
				$node->expandible = true;
				break;
		}
	}

	static function getTree($xmap, $parent, &$params)
	{
		self::initialize($params);

		$uri	= JURI::getInstance($parent->link);
		$view	= $uri->getVar('view');

		$menu	= JSite::getMenu();
		$menuparams	= $menu->getParams($parent->id);

		if ($view == 'serie') {
			$id = (int)$uri->getVar('id', 0);
		} elseif ($view == 'sermons') {
			$id = 0;
		} else { // Nothing to expand
			return;
		}

		// Cat params stuff - maybe change to serie?
		$priority	= JArrayHelper::getValue($params, 'cat_priority', $parent->priority, '');
		$changefreq	= JArrayHelper::getValue($params, 'cat_changefreq', $parent->changefreq, '');
		if ($priority == '-1')
			$priority = $parent->priority;
		if ($changefreq == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority']		= $priority;
		$params['cat_changefreq']	= $changefreq;

		// Link params stuff - maybe change to sermon?
		$priority	= JArrayHelper::getValue($params, 'link_priority', $parent->priority, '');
		$changefreq	= JArrayHelper::getValue($params, 'link_changefreq', $parent->changefreq, '');
		if ($priority == '-1')
			$priority = $parent->priority;

		if ($changefreq == '-1')
			$changefreq = $parent->changefreq;

		$params['link_priority'] = $priority;
		$params['link_changefreq'] = $changefreq;


		// Get Category, we don't need that
//		$options = array();
//		$options['countItems']	= false;
//		$options['catid']		= rand();
//		$categories = JCategories::getInstance('Sermonspeaker', $options);
//		$category = $categories->get($id? $id : 'root', true);

		xmap_com_sermonspeaker::getSermonsTree($xmap, $parent, $params, $id);
	}

	static function getSermonsTree($xmap, $parent, &$params, $id)
	{
		if($id){
			$model	= new SermonspeakerModelSerie();
			$model->getState();
			$model->setState('serie.id', $id);
		} else {
			$model	= new SermonspeakerModelSermons();
			$model->getState();
		}
		$model->setState('list.limit', JArrayHelper::getValue($params, 'max_links', NULL));
		$model->setState('list.start', 0);
		$model->setState('list.ordering', 'ordering');
		$model->setState('list.direction', 'ASC');
		$items	= $model->getItems();
		$xmap->changeLevel(1);
		foreach ($items as $item) {
			$node		= new stdclass;
			$node->id	= $parent->id;
			$node->uid	= $parent->uid . 'i' . $item->id;
			$node->name	= $item->sermon_title;
			$node->link	= SermonspeakerHelperRoute::getSermonRoute($item->id);
			$node->priority		= $params['link_priority'];
			$node->changefreq	= $params['link_changefreq'];
			$node->expandible	= false;
			$xmap->printNode($node);
		}
		$xmap->changeLevel(-1);
	}
	
	static public function initialize(&$params)
	{
		if (self::$_initialized) {
			return;
		}
		self::$_initialized = true;
		require_once JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'models'.DS.'sermons.php';
		require_once JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'models'.DS.'serie.php';
		require_once JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'helpers'.DS.'route.php';
	}
}