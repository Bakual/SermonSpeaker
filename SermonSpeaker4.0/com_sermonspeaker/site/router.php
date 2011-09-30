<?php
defined('_JEXEC') or die;

function SermonspeakerBuildRoute(&$query){
	$segments = array();
	if (isset($query['view'])){
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['task'])){
		$segments[] = $query['task'];
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
	if (isset($query['format'])){
		unset($query['format']);
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
			break;
		case 'sermons':
			$vars['view'] = 'sermons';
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
			break;
		case 'frontendupload':
			$vars['view'] = 'frontendupload';
			break;
		case 'archive':
			$vars['view'] = 'archive';
			if (isset($segments[1]) && $segments[1]){
				$vars['year'] = (int)$segments[1];
			}
			if (isset($segments[2]) && $segments[2]){
				$vars['month'] = (int)$segments[2];
			}
			break;
		case 'feed':
			$vars['view'] = 'feed';
			$vars['format'] = 'raw';
			break;
		case 'serie.download':
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
		}
	return $vars;
}