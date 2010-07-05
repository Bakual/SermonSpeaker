<?php
/**
 * @version 0.9 $Id: sermonspeaker.php 825 2008-12-03 16:12:24Z sermonspeaker $
 * @package Joomla
 * @subpackage SermonSpeaker
 * @copyright (C) 2010 Phil Boucher
 * @license GNU/GPL, see LICENCE.php
 * SermonSpeaker is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * SermonSpeaker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with SermonSpeaker; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$app->registerEvent('onSearch', 'plgSearchSermonspeaker');
$app->registerEvent('onSearchAreas', 'plgSearchSermonspeakerAreas');

//Load the Plugin language file out of the administration
JPlugin::loadLanguage('plg_search_sermonspeaker', JPATH_ADMINISTRATOR);

/**
 * @return array An array of search areas
 */
function &plgSearchSermonspeakerAreas() {
	static $areas = array(
		'spsermons' => 'PLG_SEARCH_SERMONSPEAKER_SERMONS',
		'spseries' => 'PLG_SEARCH_SERMONSPEAKER_SERIES',
	);
	return $areas;
}

/**
 * Categories Search method
 *
 * The sql must return the following fields that are
 * used in a common display routine: href, title, section, created, text,
 * browsernav
 * @param string Target search string
 * @param string mathcing option, exact|any|all
 * @param string ordering option, newest|oldest|popular|alpha|category
 * @param mixed An array if restricted to areas, null if search all
 */
function plgSearchSermonspeaker($text, $phrase='', $ordering='', $areas=null)
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();
	
	if (is_array( $areas )) {
		if (!array_intersect($areas, array_keys(plgSearchSermonspeakerAreas()))) {
			return array();
		}
	} else {
		$areas = array_keys( plgSearchSermonspeakerAreas() );
	}

	// load plugin params info
	$plugin =& JPluginHelper::getPlugin('search', 'sermonspeaker');
	$pluginParams = new JParameter($plugin->params);

	$limit = $pluginParams->def('search_limit', 50);

	$text = trim($text);
	if ( $text == '' ) {
		return array();
	}

	$searchSermonSpeaker = $db->Quote(JText::_('PLG_SEARCH_SERMONSPEAKER_SERMONS'));

	$rows = array();

	if(in_array('spsermons', $areas)) {

		switch ($phrase) {
			case 'exact':
				$search 	= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'LOWER(a.sermon_title) LIKE '.$search;
				$wheres2[] 	= 'LOWER(a.sermon_scripture) LIKE '.$search;
				$wheres2[] 	= 'LOWER(a.notes) LIKE '.$search;
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word 		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= 'LOWER(a.sermon_title) LIKE '.$word;
					$wheres2[] 	= 'LOWER(a.sermon_scripture) LIKE '.$word;
					$wheres2[] 	= 'LOWER(a.notes) LIKE '.$word;
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		
		switch ($ordering) {
			case 'oldest':
				$order = 'a.sermon_date ASC';
				break;

			case 'alpha':
				$order = 'a.sermon_title ASC';
				break;

			case 'category':
				$order = 'c.series_name ASC, a.sermon_title ASC';
				break;
      
			case 'popular':
				$order = 'a.hits DESC, a.sermon_title ASC';
				break;
					
			case 'newest':
			default:
				$order = 'a.sermon_date DESC';
		}

		$query = 'SELECT a.sermon_title AS title,'
		. ' a.notes AS text,'
		. ' a.sermon_date AS created,'
		. ' "2" AS browsernav,'
		. " CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug, \n"
		. ' CONCAT_WS( " / ", '. $searchSermonSpeaker .', c.series_title, a.sermon_title ) AS section'
		. ' FROM #__sermon_sermons AS a'
		. ' INNER JOIN #__sermon_series AS c ON c.id = a.series_id'
		. ' WHERE ( '.$where.' )'
		. ' AND a.published = 1'
		. ' AND c.published = 1'
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query, 0, $limit );
		$list = $db->loadObjectList();

		foreach($list as $key => $row) {
			$list[$key]->href = 'index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug;
		}

		$rows[] = $list;
	}

	if(in_array('spseries', $areas)) {

		switch ($phrase) {
			case 'exact':
				$search 		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'LOWER(series_title) LIKE '.$search;
				$wheres2[] 	= 'LOWER(series_description) LIKE '.$search;
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word 		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= 'LOWER(series_title) LIKE '.$word;
					$wheres2[] 	= 'LOWER(series_description) LIKE '.$word;
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}

		switch ( $ordering ) {
			case 'oldest':
				$order = 'created_on DESC';
				break;

			case 'alpha':
				$order = 'series_title ASC';
				break;

			case 'popular':
				$order = 'hits DESC, series_title ASC';
				break;
					
			case 'newest':
				$order = 'created_on ASC';
				break;
			default:
				$order = 'series_title ASC';
		}

		$query = 'SELECT series_title AS title,'
		. ' series_description AS text,'
		. ' created_on as created,'
		. ' "2" AS browsernav,'
		. ' id as slug, '
		. ' CONCAT_WS( " / ", '. $searchSermonSpeaker .', series_title )AS section'
		. ' FROM #__sermon_series'
		. ' WHERE ( '.$where.')'
		. ' AND published = 1'
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query, 0, $limit );
		$list2 = $db->loadObjectList();
    
		foreach($list2 as $key => $row) {
			$list2[$key]->href = 'index.php?option=com_sermonspeaker&view=serie&id='.$row->slug;
		}

		$rows[] = $list2;
	}


	$count = count( $rows );
	if ( $count > 1 ) {
		switch ( $count ) {
			case 2:
				$results = array_merge( (array) $rows[0], (array) $rows[1] );
				break;

			case 3:
				$results = array_merge( (array) $rows[0], (array) $rows[1], (array) $rows[2] );
				break;

			case 4:
			default:
				$results = array_merge( (array) $rows[0], (array) $rows[1], (array) $rows[2], (array) $rows[3] );
				break;
		}

		return $results;
	} else if ( $count == 1 ) {
		return $rows[0];
	}
}
?>