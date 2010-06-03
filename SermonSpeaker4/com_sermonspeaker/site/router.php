<?php
function SermonspeakerBuildRoute(&$query){
	$segments = array();
	if (isset($query['view'])){
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
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
		}
	return $vars;
}