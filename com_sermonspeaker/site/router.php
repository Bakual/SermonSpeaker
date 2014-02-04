<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

function SermonspeakerBuildRoute(&$query){
	$app		= JFactory::getApplication();
	$segments	= array();
	$view		= '';
	if (isset($query['view'])){
		$segments[] = $query['view'];
		$view = $query['view'];
		unset($query['view']);
	} else {
		// Get view from Itemid if not specified in query
		// get a menu item based on Itemid or currently active
		$menu		= $app->getMenu();
		// we need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid'])) {
			$menuItem = $menu->getActive();
		} else {
			$menuItem = $menu->getItem($query['Itemid']);
		}
		if (isset($menuItem->query['view'])){
			$view	= $menuItem->query['view'];
		}
	}
	if (isset($query['task'])){
		$segments[] = str_replace('.', '_', $query['task']);
		if (isset($query['type'])){
			$segments[] = $query['type'];
			unset($query['type']);
		}
		unset($query['task']);
	}
	if (isset($query['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (isset($query['year'])){
		$segments[] = $query['year'];
		unset($query['year']);
	}
	if (isset($query['month'])){
		$segments[] = $query['month'];
		unset($query['month']);
	}
	if(($view == 'speaker') && isset($query['layout'])){
		$segments[] = $query['layout'];
		unset($query['layout']);
	}
	if(($view == 'sitemap' || $view == 'feed') && !isset($query['format'])){
		if($app->getCfg('sef_suffix')){
			$query['format'] = 'raw';
		}
	}
	return $segments;
}

function SermonspeakerParseRoute($segments){
	$vars = array();
	switch ($segments[0]){
		case 'series':
			$vars['view'] = 'series';
			break;
		case 'seriessermon':
			$vars['view'] = 'seriessermon';
			break;
		case 'serie':
			$vars['view'] = 'serie';
			$id = explode(':', $segments[1]);
			$vars['id'] = (int)$id[0];
			if (isset($segments[2]) && $segments[2]){
				$vars['year'] = (int)$segments[2];
			}
			if (isset($segments[3]) && $segments[3]){
				$vars['month'] = (int)$segments[3];
			}
			break;
		case 'sermons':
			$vars['view'] = 'sermons';
			if (isset($segments[1]) && $segments[1]){
				$vars['year'] = (int)$segments[1];
			}
			if (isset($segments[2]) && $segments[2]){
				$vars['month'] = (int)$segments[2];
			}
			break;
		case 'sermon':
			$vars['view'] = 'sermon';
			$id = explode(':', $segments[1]);
			$vars['id'] = (int)$id[0];
			break;
		case 'speakers':
			$vars['view'] = 'speakers';
			break;
		case 'speaker':
			$vars['view'] = 'speaker';
			$id = explode(':', $segments[1]);
			$vars['id'] = (int)$id[0];
			if (isset($segments[2]) && is_numeric($segments[2]))
			{
				if ($segments[2])
				{
					$vars['year'] = (int)$segments[2];
				}
				if (isset($segments[3]) && $segments[3])
				{
					$vars['month'] = (int)$segments[3];
				}
				if(isset($segments[4]))
				{
					$vars['layout'] = $segments[4];
				}
			}
			else
			{
				if(isset($segments[2]))
				{
					$vars['layout'] = $segments[2];
				}
			}
			break;
		case 'frontendupload':
			$vars['view'] = 'frontendupload';
			break;
		case 'serieform':
			$vars['view'] = 'serieform';
			break;
		case 'speakerform':
			$vars['view'] = 'speakerform';
			break;
		case 'tagform':
			$vars['view'] = 'tagform';
			break;
		case 'feed':
			$vars['view'] = 'feed';
			$vars['format'] = 'raw';
			break;
		case 'sitemap':
			$vars['view'] = 'sitemap';
			$vars['format'] = 'raw';
			break;
		case 'serie_download':
			$vars['task'] = 'serie.download';
			$id = explode(':', $segments[1]);
			$vars['id'] = (int)$id[0];
			break;
		case 'download':
			$vars['task'] = 'download';
			$vars['type'] = $segments[1];
			$id = explode(':', $segments[2]);
			$vars['id'] = (int)$id[0];
			break;
		case 'frontendupload_edit':
			$vars['task'] = 'frontendupload.edit';
			break;
		case 'serieform_edit':
			$vars['task'] = 'serieform.edit';
			break;
		case 'speakerform_edit':
			$vars['task'] = 'speakerform.edit';
			break;
		case 'tagform_edit':
			$vars['task'] = 'tagform.edit';
			break;
		case 'scripture':
			$vars['view'] = 'scripture';
			break;
		case 'close':
			$vars['view'] = 'close';
			break;
	}
	return $vars;
}