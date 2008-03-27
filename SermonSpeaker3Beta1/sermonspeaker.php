<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.helper');
require_once(JApplicationHelper::getPath('html'));
require_once('sermoncast.php');
require(JPATH_ADMINISTRATOR.DS. 'components'.DS.$option.DS.'config.sermonspeaker.php');

switch($task)
{
  case "singlespeaker":
		$id = JRequest::getVar('id');
		singlespeaker( $option, $id ,$task);
		break;
  
  case "singleseries":
		$id = JRequest::getVar('id');
		singleseries( $option, $id,$task );
		break;
  
  case "singlesermon":
		$id = JRequest::getVar('id');
		singlesermon( $option, $id ,$task);
		break;	
  
  case "series":
		$total_pages = JRequest::getVar('total_pages');
		$curr_page = JRequest::getVar('curr_page');
		seriesmain( $option, $curr_page, $total_pages, $task  );
		break;
	
	case "showseries":
		$id = JRequest::getVar('id');
		showseries( $option, $id ,$task);
		break;
  	
  case "sermons":
		$total_pages = JRequest::getVar('total_pages');
		$curr_page = JRequest::getVar('curr_page');
		sermonmain( $option, $curr_page, $total_pages, $task  );
		break;
		
	case "latest_sermons":
		$id = JRequest::getVar('id');
		latest_sermons( $option, $id ,$task);
		break;
  
  case "search":
		$search = JRequest::getVar('search');
		searchResults( $option, $search, $task  );
		break;
		
	case "popup":
		popup( $option );
		break;
	
	case "help":
		HTML_speaker::help( $option );
		break;
	
	case "podcast":
	  feedPodcast( true );
	  break;
  	
	default:
	  $config = new sermonConfig;
		$total_pages = JRequest::getVar('total_pages');
		$curr_page = JRequest::getVar('curr_page');
		$id = JRequest::getVar('id');
		if (!$config->startpage) { $startpage = "1";} else {$startpage = $config->startpage;}
		switch($startpage) {
      case "1" :
        //SpeakerOverview
        $task="speakermain";
        speakermain( $option, $curr_page, $total_pages, $task );
        break;
      case "2" :
        //SeriesOverview
        $task="seriesmain";
        seriesmain( $option, $curr_page, $total_pages, $task );
        break;
      case "3" :
        //Sermonslist
        $task="sermonlist";
        //sermonlist( $option, $curr_page, $total_pages, $task );
        $sort = JRequest::getVar('sort');
        sermonlistsort( $option, $id ,$task, $curr_page, $total_pages, $sort);
        break;
      case "4" :
        //SeriesSermons
        $task="seriessermons";
        seriessermons( $option, $curr_page, $total_pages, $task );
        break;  
    }
    
    break;
}

######################################################
### Full Overviews                                 ###
######################################################

function speakermain( $option, $curr_page, $total_pages, $task  ) {
	$config = new sermonConfig;
	if(!$curr_page) {
		$curr_page = "1";
	}
  $limitstart = ($curr_page-1)*$config->sermonresults;
	$limitend = ($curr_page*$config->sermonresults);
	$db =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__sermon_speakers WHERE published='1'";
	$db->setQuery( $query );
	$total_rows = $db->LoadResult();
	if ($db->getErrorNum()) { 
		echo $db->stderr(); 
		return false; 
	} 	
	if( $total_rows == 0 ) {  
	   echo $lang->no_speakers;
	} else {
		$query = "SELECT * FROM #__sermon_speakers WHERE published='1' ORDER BY ordering ASC, name LIMIT $limitstart,$limitend";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		$total = count($rows);
		
		if( !$total_pages ) {
			$total_pages = ceil($total_rows/$total);
		}
		HTML_speaker::speakermain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  );
	}
} // end of speakermain

function seriesmain( $option, $curr_page, $total_pages, $task  ) {
	$config = new sermonConfig;
	if(!$curr_page) {
		$curr_page = "1";
	}
	$limitstart = ($curr_page-1)*$config->sermonresults;
	$limitend = ($curr_page*$config->sermonresults);
  $database =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__sermon_series WHERE published='1'";
	$database->setQuery( $query );
	$total_rows = $database->LoadResult();
	
  $query = 'SELECT j.id, speaker_id, l.name, series_title, series_description, j.published, j.ordering, j.hits, j.created_by, j.created_on, avatar_id, avatar_location'
        . ' FROM #__sermon_series j, #__sermon_avatars k, #__sermon_speakers l'
        . ' WHERE j.avatar_id = k.id'
        . ' AND speaker_id = l.id'
        . ' AND j.published = \'1\''
        . ' ORDER BY j.ordering, j.id desc, j.series_title'
        . ' LIMIT '.$limitstart.','.$limitend; 
              
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$total = count($rows);
	if( !$total_pages ) {
		$total_pages = ceil($total_rows/$total);
	}
	
	$query = 'SELECT COUNT( * ) FROM jos_sermon_series WHERE published = 1 AND avatar_id != 1 ';
  $database->setQuery( $query );
  $av = $database->loadResult();
  
	HTML_speaker::seriesmain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task, $av );
} // end of seriesmain


function sermonmain( $option, $curr_page, $total_pages, $task  ) {
	$config = new sermonConfig;

	if(!$curr_page) {
		$curr_page = "1";
	}

	$limitstart = ($curr_page-1)*$config->sermonresults;
	$limitend = ($curr_page*$config->sermonresults);
  $database =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__sermon_sermons WHERE published='1'";
	$database->setQuery( $query );
	$total_rows = $database->LoadResult();

	$query = "SELECT * FROM #__sermon_sermons WHERE published='1' ORDER BY speaker_id, series_id, sermon_date ASC LIMIT $limitstart,$limitend";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	echo $query;
	$total = count($rows);

	if( !$total_pages ) {
		$total_pages = ceil($total_rows/$total);
	}

	HTML_speaker::sermonmain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  );
} // end of sermonmain

######################################################
### SermonList                                     ###
######################################################

function sermonlist( $option, $curr_page, $total_pages, $task  ) {
	$config = new sermonConfig;

	if(!$curr_page) {
		$curr_page = "1";
	}

	$limitstart = ($curr_page-1)*$config->sermonresults;
	$limitend = ($curr_page*$config->sermonresults);
  $database =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__sermon_sermons WHERE published='1'";
	$database->setQuery( $query );
	$total_rows = $database->LoadResult();  
  
  $query = 'SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id'
        . ' FROM #__sermon_sermons j, #__sermon_speakers k'
        . ' WHERE j.speaker_id = k.id'
        . ' AND j.published = \'1\''
        . ' ORDER BY j.ordering, j.sermon_date desc, j.sermon_number desc'
        . ' LIMIT '.$limitstart.','.$limitend; 
  
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
  
	$total = count($rows);

	if( !$total_pages ) {
		$total_pages = ceil($total_rows/$total);
	}

	HTML_speaker::sermonlist( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  );
} // end of sermonlist


function sermonlistsort( $option, $id, $task, $curr_page, $total_pages, $sort ) {
  $config = new sermonConfig;
    
  if(!$curr_page) { $curr_page = "1"; }
  if(!$sort) { $sort = "sermondate"; }
    
  $limitstart = ($curr_page-1)*$config->sermonresults;
  $limitend = ($curr_page*$config->sermonresults);
  $database =& JFactory::getDBO();
  
  //$query = "SELECT count(*) FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1'";
  $query = "SELECT count(*) FROM #__sermon_sermons WHERE published='1'";
  $database->setQuery( $query );
  $total_rows = $database->LoadResult();
  
  if( $total_rows == 0 ) {  
    echo $lang->no_sermons;
  } else {
    if ($sort == "sermondate") {
      //$query = "SELECT * FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1' ORDER BY sermon_date desc, sermon_number desc LIMIT $limitstart,$limitend";
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_date desc, j.sermon_number desc LIMIT $limitstart,$limitend"; 
    } else if ($sort == "mostrecentlypublished") {
      //$query = "SELECT * FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1' ORDER BY id desc, sermon_number desc LIMIT $limitstart,$limitend";
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.id desc, j.sermon_number desc LIMIT $limitstart,$limitend";
    } else if ($sort == "mostviewed") {
      //$query = "SELECT * FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1' ORDER BY hits desc, sermon_number desc LIMIT $limitstart,$limitend";
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.hits desc, j.sermon_number desc LIMIT $limitstart,$limitend";
    } else if ($sort == "alphabetically") {
      //$query = "SELECT * FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1' ORDER BY sermon_title asc, sermon_number desc LIMIT $limitstart,$limitend";
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_title asc, j.sermon_number desc LIMIT $limitstart,$limitend";
    } else {
      //$query = "SELECT * FROM #__sermon_sermons WHERE speaker_id='$id' AND published='1' ORDER BY sermon_date desc, sermon_number desc LIMIT $limitstart,$limitend";
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name,j.id FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_date desc, j.sermon_number desc LIMIT $limitstart,$limitend";
    }
    
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
    $total = count($rows);
    
    if( !$total_pages && $total>0 ) { $total_pages = ceil($total_rows/$total); }
  } // end of if
  
  HTML_speaker::sermonlist( $option, $id, $task, $curr_page, $total_pages, $total_rows, $rows, $sort );
} // end of sermonlistsort

######################################################
### Single Items                                   ###
######################################################
function singlespeaker( $option, $id, $task ) {
  global $mainframe;
	$config = new sermonConfig;
  $database =& JFactory::getDBO();
  
  if ($config->track_speaker) { updateStat("speakers", $id); }
  
	$query = "SELECT * FROM #__sermon_sermons WHERE id='$id'";
	$database->setQuery( $query );
	$row = $database->loadObjectList();
  
  $breadcrumbs = & $mainframe->getPathWay();
	$breadcrumbs->addItem( 'Series', JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
  
	HTML_speaker::singlespeaker( $option, $row, $config, $task );
} // end of singlespeaker

function singlesermon( $option, $id, $task ) {
  global $mainframe;
	$config = new sermonConfig;
	$lang = new sermonLang;
	$database =& JFactory::getDBO();
	
	if ($config->track_sermon) { updateStat("sermons", $id); }
  
  $query = "SELECT * FROM #__sermon_sermons WHERE id='$id'";
	$database->setQuery( $query );
	$row = $database->loadObjectList();
  
  $query = "SELECT series_title FROM #__sermon_series WHERE id='$id'";
	$database->setQuery( $query );
	$seriesname = $database->loadResult();
  
  $breadcrumbs = & $mainframe->getPathWay();
  if ($config->startpage == 1) {
    if (strpos($_SERVER['HTTP_REFERER'],'task=latest_sermons')) {
      $query = "SELECT speaker_id FROM #__sermon_sermons WHERE id='$id'";
	    $database->setQuery( $query );
	    $speaker_id = $database->loadResult();
    	$breadcrumbs->addItem( $lang->latest_sermons, JRoute::_( 'index.php?option=com_sermonspeaker&task=latest_sermons&id='.$speaker_id ) );
    	$breadcrumbs->addItem( $row[0]->sermon_title, '' );
    } else {
      $breadcrumbs->addItem( $lang->series, JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
    	$breadcrumbs->addItem( $seriesname, JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
    	$breadcrumbs->addItem( $row[0]->sermon_title, '' );
    }
  }
  if ($config->startpage == 2) {
    $query = "SELECT a.series_title FROM #__sermon_series a, #__sermon_sermons b WHERE b.series_id = a.id AND b.id='$id'";
	  $database->setQuery( $query );
	  $seriesname = $database->loadResult();
    $breadcrumbs = & $mainframe->getPathWay();
    $breadcrumbs->addItem( $lang->series, JRoute::_( 'index.php?option=com_sermonspeaker&id='.$id ) );
    $breadcrumbs->addItem( $seriesname, JRoute::_( 'index.php?option=com_sermonspeaker&task=singleseries&id='.$id ) );
  }
  if ($config->startpage == 3) {
    $breadcrumbs = & $mainframe->getPathWay();
    $breadcrumbs->addItem( $lang->sermonlist, JRoute::_( 'index.php?option=com_sermonspeaker&id='.$id ) );
  }
  
	HTML_speaker::singlesermon( $option, $row, $config, $task );
} // end of singlesermon

function singleseries( $option, $id, $task   ) {
  global $mainframe;
	$config = new sermonConfig;
	$lang = new sermonLang;

	$database =& JFactory::getDBO();
	
	if ($config->track_series) { updateStat("series", $id); }
  
  $query = "SELECT series_title FROM #__sermon_series WHERE id='$id'";
	$database->setQuery( $query );
	$seriesname = $database->loadResult();
  if ($config->startpage == 1) {
    $breadcrumbs = & $mainframe->getPathWay();
    $breadcrumbs->addItem( $lang->series, JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
  	$breadcrumbs->addItem( $seriesname, JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
  }
  if ($config->startpage == 2) {
    $breadcrumbs = & $mainframe->getPathWay();
    $breadcrumbs->addItem( $lang->series, JRoute::_( 'index.php?option=com_sermonspeaker&id='.$id ) );
  }
  
	HTML_speaker::showseries( $option, $id, $task  );
} // end of singleseries

function latest_sermons( $option, $id, $task  ) {
  global $mainframe;
  $config = new sermonConfig;
  $lang = new sermonLang;
  
  if ($config->track_speaker) { updateStat("speakers", $id); }
  
  $breadcrumbs = & $mainframe->getPathWay();
  $breadcrumbs->addItem( $lang->latest_sermons, JRoute::_( 'index.php?option=com_sermonspeaker&task=singlespeaker&id='.$id ) );
  
	HTML_speaker::show_latest_sermons( $option, $id, $task );
} // end of latest_sermons

######################################################
### SeriesSermons                                  ###
######################################################

function seriessermons( $option, $curr_page, $total_pages, $task  ) {
	$config = new sermonConfig;
	if(!$curr_page) {
		$curr_page = "1";
	}
	$limitstart = ($curr_page-1)*$config->sermonresults;
	$limitend = ($curr_page*$config->sermonresults);
  $database =& JFactory::getDBO();
	$query = "SELECT count(*) FROM #__sermon_series WHERE published='1'";
	$database->setQuery( $query );
	$total_rows = $database->LoadResult();  
  
  $query = 'SELECT j.id, j.series_title , j.series_description , k.name , l.avatar_location '
        . ' FROM #__sermon_series j , #__sermon_speakers k , jos_sermon_avatars l '
        . ' WHERE j.speaker_id = k.id '
        . ' AND j.published = 1 '
        . ' AND j.avatar_id = l.id '
        . ' ORDER BY j.ordering , j.id desc '
        . ' LIMIT '.$limitstart.','.$limitend;  
  
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$total = count($rows);

	if( !$total_pages ) {
		$total_pages = ceil($total_rows/$total);
	}

	HTML_speaker::seriessermons( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  );
} // end of seriessermons

######################################################
### Search                                         ###
######################################################
function searchResults( $option, $search, $task  ) {
	$config = new sermonConfig;
  
  $database =& JFactory::getDBO();
  
	$query = "SELECT id, name FROM #__sermon_speakers WHERE name LIKE '%$search%' AND published='1'";
	$database->setQuery( $query );
	$res = $database->query();
	$speakers = $database->loadObjectList();
	$count = $database->getNumRows($res);
	if ($count == 0 ) {$speakers = 0;}

	$query = "SELECT id, series_title FROM #__sermon_series WHERE series_title LIKE '%$search%' AND published='1'";
	$database->setQuery( $query );
	$res = $database->query();
	$seriess = $database->loadObjectList();
	$count = $database->getNumRows($res);
	if ($count == 0 ) {$seriess = 0;}

	$query = "SELECT id, sermon_title FROM #__sermon_sermons WHERE sermon_title LIKE '%$search%' AND published='1'";
	$database->setQuery( $query );
	$res = $database->query();
	$sermons = $database->loadObjectList();
	$count = $database->getNumRows($res);
	if ($count == 0 ) {$sermons = 0;}

	HTML_speaker::searchResults( $option, $speakers, $seriess, $sermons, $config, $task  );
} // end of searchResults

function updateStat ($type, $id) {
  switch($type) {
    case "speakers" : 
      $db = "#__sermon_speakers";
      break;   
    case "sermons" :
      $db = "#__sermon_sermons";
      break;
    case "series" :
      $db = "#__sermon_series";
      break;
  } // of switch
  $database =& JFactory::getDBO();
  $query = "UPDATE ".$db." SET hits=hits+1 WHERE id=$id;";
  $database->setQuery( $query );
  $database->query();
} // end of updateStat

######################################################
### Paginate                                       ###
######################################################

function paginate_sort($pid, $total, $langpage, $langof, $option, $task, $id, $Itemid, $sort, $isBeginning) {
  $config = new sermonConfig;
  $lang = new sermonLang;
  $display = $config->sermonresults;
  // find out how many pages we have
  $pages = ($total <= $display) ? 1 : ceil($total / $display);
  if($pid > $pages)
      $pid = $pages;
  if($isBeginning)
      echo $langpage.' '.$pid.' '.$langof.' '.$pages.'<br />';
      
  // create the links
  $first = '<a title="'.$lang->first.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=1&amp;Itemid=$Itemid" ).'">&#171;</a>';
  $prev = '<a title="'.$lang->previous.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=".($pid-1)."&amp;Itemid=$Itemid" ).'">&#139; '.$lang->prev.'</a>';
  $next = '<a title="'.$lang->next.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=".($pid+1)."&amp;Itemid=$Itemid" ).'">'.$lang->nxt.' &#155;</a>';
  $last = '<a title="'.$lang->last.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=$pages&amp;Itemid=$Itemid" ).'">&#187;</a>';
 
  // display opening navigation 
  echo '<div class="Pages"><div class="Paginator">';
  echo ($pid > 1) ? "$first : $prev : " : ''/*'&#171; : &#139; Prev : '*/;

  // limit the number of page links displayed 
  $begin = $pid - 4;
  while($begin < 1)
      $begin++;
  $end = $pid + 4;
  while($end > $pages)
      $end--;
  for($i=$begin; $i<=$end; $i++)
  echo ($i == $pid) ? ' <span class="this-page">'.$i.'</span> ' : ' <a title="Page '.$i.'" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=$i&amp;Itemid=$Itemid" ).'">'.$i.'</a>';
  
  // display ending navigation 
  echo ($pid < $pages) ? " : $next : $last " : ''/*' : Next &#155; : &#187;'*/;
  echo '</div></div>';
} // end of paginate_sort

function paginate($pid, $total, $langpage, $langof, $option, $task, $id, $Itemid, $isBeginning) {
  $config = new sermonConfig;
  $lang = new sermonLang;
  $display = $config->sermonresults;
  // find out how many pages we have
  $pages = ($total <= $display) ? 1 : ceil($total / $display);
  if($pid > $pages)
      $pid = $pages;
  if($isBeginning)
      echo $langpage.' '.$pid.' '.$langof.' '.$pages.'<br />';
      
  // create the links
  $first = '<a title="'.$lang->first.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=1&amp;Itemid=$Itemid" ).'">&#171;</a>';
  $prev = '<a title="'.$lang->previous.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=".($pid-1)."&amp;Itemid=$Itemid" ).'">&#139; '.$lang->prev.'</a>';
  $next = '<a title="'.$lang->next.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=".($pid+1)."&amp;Itemid=$Itemid" ).'">'.$lang->nxt.' &#155;</a>';
  $last = '<a title="'.$lang->last.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=$pages&amp;Itemid=$Itemid" ).'">&#187;</a>';
 
  /* display opening navigation */
  echo '<div class="Pages"><div class="Paginator">';
  echo ($pid > 1) ? "$first : $prev : " : ''/*'&#171; : &#139; Prev : '*/;

  /* limit the number of page links displayed */
  $begin = $pid - 4;
  while($begin < 1)
      $begin++;
  $end = $pid + 4;
  while($end > $pages)
      $end--;
  for($i=$begin; $i<=$end; $i++)
  echo ($i == $pid) ? ' <span class="this-page">'.$i.'</span> ' : ' <a title="Page '.$i.'" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=$i&amp;Itemid=$Itemid" ).'">'.$i.'</a>';
  
  /* display ending navigation */
  echo ($pid < $pages) ? " : $next : $last " : ''/*' : Next &#155; : &#187;'*/;
  echo '</div></div>';
} // end of paginate

?>
