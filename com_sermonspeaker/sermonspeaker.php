<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

jimport('joomla.application.helper');
require_once(JApplicationHelper::getPath('html'));
require_once('sermoncast.php');
require(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.sermonspeaker.php');

if (!$task) {$task = JRequest::getString('task');}
$config = new sermonConfig;

if ($config->fu_taskname == "" && $config->fu_enable == "0") {$config->fu_taskname = "dummy";}

switch($task) {
  case "singlespeaker":
		$id = JRequest::getInt('id');
		singlespeaker( $option, $id ,$task);
		break;
  
  case "singleseries":
		$id = JRequest::getInt('id');
		$total_pages = JRequest::getInt('total_pages');
		$curr_page = JRequest::getInt('curr_page');
		singleseries( $option, $id, $task, $curr_page, $total_pages );
		break;
  
  case "singlesermon":
		$id = JRequest::getInt('id');
		singlesermon( $option, $id ,$task);
		break;	
  
  case "series":
		$total_pages = JRequest::getInt('total_pages');
		$curr_page = JRequest::getInt('curr_page');
		seriesmain( $option, $curr_page, $total_pages, $task  );
		break;
	
	case "showseries":
		$id = JRequest::getInt('id');
		showseries( $option, $id ,$task);
		break;
  	
  case "sermons":
		$total_pages = JRequest::getInt('total_pages');
		$curr_page = JRequest::getInt('curr_page');
		sermonmain( $option, $curr_page, $total_pages, $task  );
		break;
		
	case "latest_sermons":
		$id = JRequest::getInt('id');
		latest_sermons( $option, $id ,$task);
		break;
  
  case "search":
		$search = JFilterInput::clean(JRequest::getString('search'));
		searchResults( $option, $search, $task  );
		break;
		
	case "speakerpopup":
	  $id = JRequest::getInt('id');
		speakerpopup( $option, $id );
		break;
		
	case "popup_player":
    $id = JRequest::getInt('id');
		popup_player( $option, $id );
		break;
	
	case "help":
		HTML_speaker::help( $option );
		break;
	
	case "podcast":
	  feedPodcast( true );
	  break;
	  
  case "sc_help":
    sc_help();
    break;
    
  case "dl":
    $id = JRequest::getInt('id');
    download($id);
    break;
	  
	case "sermonarchive":
    $total_pages = JRequest::getInt('total_pages');
		$curr_page = JRequest::getInt('curr_page');
    $id = JRequest::getInt('id');
    $sort = JFilterInput::clean(JRequest::getVar('sort'),string);
    $year = JRequest::getInt('year');
    $month = JFilterInput::clean(JRequest::getVar('month', ''),string);
    SermonArchive( $option, $id ,$task, $curr_page, $total_pages, $sort, $year, $month );
    break;
  
	default: 
	  //$config = new sermonConfig;
		$total_pages = JRequest::getInt('total_pages');
		$curr_page = JRequest::getInt('curr_page');
		$id = JRequest::getInt('id');
		if (!$config->startpage) { $startpage = "1";} else {$startpage = $config->startpage;}
		
		if ($config->allow_ussp == "1") {
		  $ussp = JRequest::getInt('myussp');
		  $session =& JFactory::getSession();
		  if ($ussp) { 
        $startpage = $ussp;
        $session->set("ss_ussp",$ussp);
      } else {
        $startpage = $config->startpage;
        //$startpage = $session->get("ss_ussp");
      }
    }
    
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
        $sort = JFilterInput::clean(JRequest::getVar('sort'),string);
        sermonlistsort( $option, $id ,$task, $curr_page, $total_pages, $sort);
        break;
      case "4" :
        //SeriesSermons
        $task="seriessermons";
        seriessermons( $option, $curr_page, $total_pages, $task );
        break;  
    }
    
    break;
    
    case $config->fu_taskname: {
    if ($config->fu_enable) {
      $step = JRequest::getInt('step','9');
      switch ($step) {
        case "3": fu_step3(); break;
        case "2": fu_step2(); break;
        case "1": fu_step1(); break;
        case "0": fu_logout(); break;
        default : fu_login(); break;           
      } // end of switch
    } // end of if
    break;
  } // end of case
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
		$query = "SELECT * FROM #__sermon_speakers WHERE published='1' ORDER BY ordering ASC, name LIMIT $limitstart,$config->sermonresults";
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
        . ' LIMIT '.$limitstart.','.$config->sermonresults; 
              
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$total = count($rows);
	if( !$total_pages ) {
		$total_pages = ceil($total_rows/$total);
	}
	
	$query = 'SELECT COUNT( * ) FROM #__sermon_series WHERE published = 1 AND avatar_id != 1 ';
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

	$query = "SELECT * FROM #__sermon_sermons WHERE published='1' ORDER BY speaker_id, series_id, sermon_date ASC LIMIT $limitstart,$config->sermonresults";
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
        . ' LIMIT '.$limitstart.','.$config->sermonresults; 
  
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
  
  $query = "SELECT count(*) FROM #__sermon_sermons WHERE published='1'";
  $database->setQuery( $query );
  $total_rows = $database->LoadResult();

  if( $total_rows == 0 ) {  
    echo $lang->no_sermons;
  } else {
    if ($sort == "sermondate") {
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name, k.pic, k.id as s_id, j.id, j.addfile, j.addfileDesc FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_date desc, (j.sermon_number+0) desc LIMIT $limitstart,$config->sermonresults"; 
    } else if ($sort == "mostrecentlypublished") {
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name, k.pic, k.id as s_id, j.id, j.addfile, j.addfileDesc FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.id desc, (j.sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    } else if ($sort == "mostviewed") {
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name, k.pic, k.id as s_id, j.id, j.addfile, j.addfileDesc FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.hits desc, (j.sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    } else if ($sort == "alphabetically") {
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name, k.pic, k.id as s_id, j.id, j.addfile, j.addfileDesc FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_title asc, (j.sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    } else {
      $query = "SELECT sermon_title, sermon_number, sermon_scripture, sermon_date, sermon_time, notes, k.name, k.pic, k.id as s_id, j.id, j.addfile, j.addfileDesc FROM #__sermon_sermons j, #__sermon_speakers k WHERE j.speaker_id = k.id AND j.published='1' ORDER BY j.sermon_date desc, (j.sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
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
  
	$query = "SELECT * FROM #__sermon_speakers WHERE id='$id'";
	$database->setQuery( $query );
	$row = $database->loadObject();
  
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

function singleseries( $option, $id, $task, $curr_page, $total_pages ) {
  global $mainframe;
	$config = new sermonConfig;
	$lang = new sermonLang;
  
  if(!$curr_page) { $curr_page = "1"; }
  $limitstart = ($curr_page-1)*$config->sermonresults;
  $limitend = ($curr_page*$config->sermonresults);
	$database =& JFactory::getDBO();
	
	if ($config->track_series) { updateStat("series", $id); }
  
  $query="SELECT count(*) FROM #__sermon_sermons a, #__sermon_speakers b WHERE a.series_id='".$id."'and a.speaker_id = b.id AND a.published='1'";
  $database->setQuery( $query );
  $total_rows = $database->LoadResult();
  
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
  
  if( $total_rows == 0 ) {  
    echo $lang->no_sermons;
  } else {
    $query="SELECT a.*, b.name FROM #__sermon_sermons a, #__sermon_speakers b WHERE a.series_id='".$id."'and a.speaker_id = b.id AND a.published='1' order by a.sermon_date, (sermon_number+0) desc LIMIT $limitstart, $config->sermonresults";
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
    $total = count($rows);
    
    if( !$total_pages && $total>0 ) { $total_pages = ceil($total_rows/$total); }
  } // end of if
  
	HTML_speaker::showseries( $option, $id, $task, $total_rows, $total_pages, $curr_page, $rows); 
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
  
  $query = 'SELECT j.id, j.series_title , j.series_description , k.name , l.avatar_location'
        . ' FROM #__sermon_series j , #__sermon_speakers k , #__sermon_avatars l '
        . ' WHERE j.speaker_id = k.id '
        . ' AND j.published = 1 '
        . ' AND k.published = 1 '
        . ' AND j.avatar_id = l.id '
        . ' ORDER BY j.ordering , j.id desc '
        . ' LIMIT '.$limitstart.','.$config->sermonresults;  
  
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
function searchResults( $option, $searchFor, $task  ) {
	$config = new sermonConfig;
  
  $db =& JFactory::getDBO();
  
  $esc = $db->getEscaped($searchFor, true);
  $search = $db->Quote('%' . $esc . '%', false);
  
  $database =& JFactory::getDBO();
  
	$query = "SELECT id, name FROM #__sermon_speakers WHERE name LIKE ".$search." AND published='1'";
	$database->setQuery( $query );
	$res = $database->query();
	$speakers = $database->loadObjectList();
	$count = $database->getNumRows($res);
	if ($count == 0 ) {$speakers = 0;}

	$query = "SELECT id, series_title FROM #__sermon_series WHERE series_title LIKE ".$search." AND published='1'";
	$database->setQuery( $query );
	$res = $database->query();
	$seriess = $database->loadObjectList();
	$count = $database->getNumRows($res);
	if ($count == 0 ) {$seriess = 0;}

	$query = "SELECT id, sermon_title FROM #__sermon_sermons WHERE sermon_title LIKE ".$search." AND published='1'";
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
  $id = (int)$id;
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

######################################################
### for mod_sermonarchive                          ###
######################################################
function SermonArchive( $option, $id, $task, $curr_page, $total_pages, $sort, $year, $month ) {
  global $mainframe;
  $config = new sermonConfig;
    
  if(!$curr_page) {
    $curr_page = "1";
  }
    
  $limitstart = ($curr_page-1)*$config->sermonresults;
  $limitend = ($curr_page*$config->sermonresults);
  
  $database =& JFactory::getDBO();
  $query = "SELECT count(*) FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month'";
  $database->setQuery( $query );
  $total_rows = $database->LoadResult();
  
  if( $total_rows == 0 ) {  
    echo $lang->no_sermons;
  } else {
    if(!$sort) {
      $sort = "date";
    }
    if($sort == "date")
      $query = "SELECT * FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month' ORDER BY sermon_date desc, (sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    else if($sort == "publishedorder")
      $query = "SELECT * FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month' ORDER BY id desc, (sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    else if($sort == "mostviewed")
      $query = "SELECT * FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month' ORDER BY hits desc, (sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    else if($sort == "alphabetically")
      $query = "SELECT * FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month' ORDER BY sermon_title asc, (sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    else
      $query = "SELECT * FROM #__sermon_sermons WHERE published='1' AND YEAR( sermon_date )='$year' AND MONTH( sermon_date )='$month' ORDER BY sermon_date desc, (sermon_number+0) desc LIMIT $limitstart,$config->sermonresults";
    
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
    $total = count($rows);
    
    $breadcrumbs = & $mainframe->getPathWay();
    
    if( !$total_pages && $total>0 ) { $total_pages = ceil($total_rows/$total); }
  }

  HTML_speaker::showSermonArchive( $option, $id, $task, $curr_page, $total_pages, $total_rows, $rows, $sort, $year, $month );
} // end of SermonArchive

function paginate_sort_year_month($pid, $total, $langpage, $langof, $option, $task, $id, $Itemid, $sort, $isBeginning, $year, $month) {
    $config = new sermonConfig;
    $display = $config->sermonresults;
    // find out how many pages we have
    $pages = ($total <= $display) ? 1 : ceil($total / $display);
    if($pid > $pages)
      $pid = $pages;
    if($isBeginning)
      echo $langpage.' '.$pid.' '.$langof.' '.$pages.'<br />';
        
    // create the links
    $first = '<a title="'.$lang->first.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=1&amp;year=$year&amp;month=$month&amp;Itemid=$Itemid" ).'">&#171;</a>';
    $prev = '<a title="'.$lang->previous.'" class="Prev" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=".($pid-1)."&amp;year=$year&amp;month=$month&amp;Itemid=$Itemid" ).'">&#139; '.$lang->prev.'</a>';
    $next = '<a title="'.$lang->next.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=".($pid+1)."&amp;year=$year&amp;month=$month&amp;Itemid=$Itemid" ).'">'.$lang->nxt.' &#155;</a>';
    $last = '<a title="'.$lang->last.'" class="Next" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;curr_page=$pages&amp;year=$year&amp;month=$month&amp;Itemid=$Itemid" ).'">&#187;</a>';
      
    if($pages > 1) {
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
      echo ($i == $pid) ? ' <span class="this-page">'.$i.'</span> ' : ' <a title="Page '.$i.'" href="'.JRoute::_( "index.php?option=$option&amp;task=$task&amp;id=$id&amp;sort=$sort&amp;curr_page=$i&amp;year=$year&amp;month=$month&amp;Itemid=$Itemid" ).'">'.$i.'</a>';
      
      /* display ending navigation */
      echo ($pid < $pages) ? " : $next : $last " : ''/*' : Next &#155; : &#187;'*/;
      echo '</div></div>';
  }
} // end of paginate_sort_year_month

function speakerpopup ($option, $id) {
  global $mainframe;
	$config = new sermonConfig;
	$lang = new sermonLang;
	
	$database =& JFactory::getDBO();
	$query="SELECT * FROM #__sermon_speakers WHERE id=".$id.";";
  $database->setQuery( $query );
  $row = $database->loadObjectList();
  
  ?>
  <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
  <tbody>
    <tr>&nbsp;</tr>
    <tr>
      <td style="width: 50px;"> </td>
      <td style="width: 1025px;"><h3><?php echo $row[0]->name;?></h3></td>
      <td style="width: 300px;"><img alt="<?php echo $row[0]->name;?>"src="<?php echo $row[0]->pic;?>"></td>
    </tr>
    <tr>
      <td style="width: 50px;"> </td>
      <td colspan="2" rowspan="1" style="width: 1000px;">
      <?php
      echo "<br /><A HREF=\"".$row[0]->website."\" target=\"blank\" title=\"".$lang->web_link_description."\">".$lang->web_link_tag.$row[0]->name."</A><br />";
      if($config->speaker_intro && $row[0]->intro) {
        echo "<br />".$row[0]->intro."<br />";
      }
      if ($row[0]->bio) { 
        echo "<br /><b>".$lang->bio.": </b><br />".$row[0]->bio."<br />";
      } 
      ?>
      </td>
      <td style="width: 50px;"> </td>
    </tr>
  </tbody>
</table>

<?php  
} // end of speakerpopup

function popup_player ( $option, $id ) {
global $mainframe;
	$config = new sermonConfig;
	$lang = new sermonLang;
	
	$database =& JFactory::getDBO();
	$query="SELECT * FROM #__sermon_sermons WHERE id=".$id.";";
  $database->setQuery( $query );
  $row = $database->loadObjectList();
  if ($config->popup_color) echo "<body bgcolor=\"#".$config->popup_color."\">\n";
  ?>
  <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
  <tbody>
    <tr>&nbsp;</tr>
    <tr><td style="width: 50px;"> </td></tr>
  <?php
  if (substr($row[0]->sermon_path,0,7) == "http://"){
    $lnk = $row[0]->sermon_path;
  } else {  
    $lnk = HTML_speaker::makelink($row[0]->sermon_path); 
  }
  echo "<tr><td style=\"width: 20px;\"> </td>";
  echo "<td><h3>".$row[0]->sermon_title."</h3></td></tr>";
  echo "<tr><td style=\"width: 20px;\"> </td> <td>";  
  if(strcasecmp(substr($lnk,-4),".mp3") == 0) {
    echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"200\" height=\"20\" allowfullscreen=\"true\" allowscriptaccess=\"always\" flashvars=\"&file=".$lnk."&autostart=".$start."&height=20&width=200".$callback."\" />";
  }
  if(strcasecmp(substr($lnk,-4),".flv") == 0) {
    echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"".$config->mp_width."\" height=\"".$config->mp_height."\" allowfullscreen=\"true\" allowscriptaccess=\"always\" flashvars=\"&file=".$lnk."&autostart=".$start."&height=".$config->mp_height."&width=".$config->mp_width.$callback."\" />";
  }
  if(strcasecmp(substr($lnk,-4),".wmv") == 0) {
    echo "<object id=mediaplayer width=400 height=323 classid=clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95 type=application/x-oleobject>
            <param name=filename value=$lnk>
            <param name=autostart value=".$start.">
            <param name=transparentatstart value=true>
            <param name=showcontrols value=1>
            <param name=showdisplay value=0>
            <param name=showstatusbar value=1>
            <param name=autosize value=1>
            <param name=animationatstart value=false>
        <embed name=\"MediaPlayer\" src=$lnk width=".$config->mp_width." height=".$config->mp_height." type=application/x-mplayer2 autostart=".$startwmp." showcontrols=1 showstatusbar=1 transparentatstart=1 animationatstart=0 loop=false pluginspage=http://www.microsoft.com/windows/windowsmedia/download/default.asp></embed>
        </object>";
  }
  echo "</td></table>";

} // end of popup_player

function sc_help () {
  global $mainframe;
	$conf = & new sermonCastConfig;
	$lang = new sermonLang;
	?>
	<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
    <tbody>
      <tr style="height: 50px;">&nbsp;</tr>
      <tr>
        <td style="width: 50px;"> </td>
        <td>
        <?php 
          if ($lang->sc_helpeditor != "") { echo $lang->sc_helpeditor; } else { echo $conf->sc_helpeditor; }
        ?>
        </td>
        <td style="width: 50px;"> </td>
      </tr>
    </tbody>
  </table>
  <?php
} // end of sc_help

function download ($id) {
  $database =& JFactory::getDBO();
	$query="SELECT sermon_path FROM #__sermon_sermons WHERE id=".$id.";";
  $database->setQuery( $query );
  $result = rtrim($database->loadResult());
  if (substr($row[0]->sermon_path,0,7) == "http://"){
    exit;
  }
  $file = str_replace('\\','/',JPATH_ROOT.$result);
  $filename = explode("/", $file ); 
  $filename = array_reverse($filename); 
  
  if(ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
  }
  
  if ( file_exists($file) ) {
    header("Pragma: public");
    header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/mp3");
    header('Content-Disposition: attachment; filename="'.$filename[0].'"');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".@filesize($file));
    set_time_limit(0);
    @readfile($file) OR die("<html><body OnLoad=\"javascript: alert('Unable to read file!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
    exit;
  } else {
    die("<html><body OnLoad=\"javascript: alert('File not found!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
  }
} // end of download

function fu_login () {
  $config = new sermonConfig;
  $lang = new sermonLang;
  
  $pwd = JFilterInput::clean(JRequest::getVar('pwd'),string);
  if ($pwd) {
    // Form was submitted
    $parts = explode( ':', $config->fu_pwd );
    $salt = $parts[1];
    $pword = sha1($pwd.$salt).":".$salt;
    if ($pword == $config->fu_pwd) {
      $_SESSION['loggedin'] = "loggedin";
      header('HTTP/1.1 303 See Other');
      header('Location: index.php?option=com_sermonspeaker&task='.$config->fu_taskname.'&step=1');
    } else {
      header('HTTP/1.1 303 See Other');
      header('Location: index.php?option=com_sermonspeaker&task='.$config->fu_taskname.'&step=9');
      }
  } else {
    ?>
    <table border="0">
      <tr>
        <td width ="50"></td><td><h1><?php echo $lang->fu_welcome; ?>!</h1><h4><?php echo $lang->fu_login; ?>...</h4></td>
      </tr>
      <tr>
        <td width ="50"></td><td>
          <form name="fu_login" method="post" enctype="multipart/form-data" >
            <?php echo $lang->fu_pwd; ?>:
            <input class="inputbox" type="password" name="pwd" size="40">
            <br/>&nbsp;<br/>
            <input type="submit" value=" <?php echo $lang->fu_log; ?> ">&nbsp;
            <input type="reset" value=" <?php echo $lang->fu_reset; ?> ">
          </form>
          <br/>&nbsp;<br/>
        <td>
      </tr>
    </table>
    <?php
  } // end of if
} //end of fu_login

function fu_step1 () {
  fu_security();
  $config = new sermonConfig;
  $lang = new sermonLang;
  $file = JRequest::getVar('upload', null, 'files', 'array');
  
  if($file) { 
    // Form was submited
    jimport('joomla.filesystem.file');
    jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
    $filename = JFile::makeSafe($file['name']);
    $dest = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".DS.$config->fu_destdir.DS.$filename;
    if (file_exists($dest)) {
    	header('Location: index.php?option=com_sermonspeaker&task='.$config->fu_taskname.'&step=3&reason=exists');
    	exit;
    }
    $allowed = array('mp3','wmv','flv');
    if (in_array(strtolower(JFile::getExt($filename)), $allowed)) {
      if ( JFile::upload($file['tmp_name'], $dest) ) {
        header('Location: index.php?option=com_sermonspeaker&task='.$config->fu_taskname.'&step=2&filename='.$filename);
        exit;
      } else {
        //Redirect and throw an error message
        echo "<br>Error";
      }
    } else { 
      ?>
      <table border="0">
        <tr>
          <td width ="50"></td><td><h1><?php echo $lang->fu_ext; ?>!</h1></td>
         </tr>
         <tr>
           <td width ="50"></td><td><b>
             <?php echo "<a href=\"index.php?option=com_sermonspeaker&task=".$config->fu_taskname."&step=1\">".$lang->fu_cont."</a>"; ?></b>
           </td>
         </tr>
         <tr><td colspan ="4">&#160;</td></tr>
         <tr>
           <td width ="50"></td><td>
           <br/>&nbsp;<br/>
           <td>
         </tr>
         <tr>
           <td width ="50"></td><td>
             <?php echo fu_logoffbtn(); ?>
           </td>
         </tr>
      </table> 
      <?php     
    }
  } else {
    // First call...
    ?>
    <table border="0">
      <tr>
        <td width ="50"></td><td><h1><?php echo $lang->fu_newsermon; ?>!</h1></td>
      </tr>
      <tr>
        <td width ="50"></td><td><b><?php echo $lang->fu_step; ?> 1 : </b><?php echo $lang->fu_step1; ?></td>
      </tr>
      <tr><td colspan ="4">&#160;</td></tr>
      <tr>
        <td width ="50"></td><td>
          <form name="fu_uploader" method="post" enctype="multipart/form-data" >
            File to upload:
            <input class="inputbox" type="file" name="upload" id="upload" size="60">
            <br>
            <input type="submit" value=" <?php echo $lang->fu_save; ?> ">&nbsp;
            <input type="reset" value=" <?php echo $lang->fu_reset; ?> ">
          </form>
        <br/>&nbsp;<br/>
        <td>
      </tr>
      <tr>
        <td width ="50"></td><td>
          <?php
          echo fu_logoffbtn();
          ?>
        </td>
      </tr>
    </table> 
    <?php
  }
} // end of fu_step1

function fu_step2() {
  fu_security();
  
  $config = new sermonConfig;
  $lang = new sermonLang;
  $vars = JRequest::get();
  $notes = JRequest::getVar('notes','','','STRING',JREQUEST_ALLOWHTML);
  if ($vars[published] != "") {
    // Form was sumitted
    $file = JRequest::getVar('filename');
    $path = DS."components".DS."com_sermonspeaker".DS."media".DS.$config->fu_destdir.DS.$file;
    $db =& JFactory::getDBO();
    $path = $db->quote( $db->getEscaped($path),false );
    $title =  $db->quote( $db->getEscaped($vars[sermon_title]),false );
    $notes =  $db->quote( $db->getEscaped($notes),false );
    $script =  $db->quote( $db->getEscaped($vars[sermon_scripture]),false );
    $nbr =  $db->quote( $db->getEscaped($vars[sermon_number]),false );
    $date =  $db->quote( $db->getEscaped($vars[sermon_date]),false );
    $tarr = explode(":",$db->getEscaped($vars[sermon_time]));
    if (sizeof($tarr) == 2) {
      if (strlen($tarr[0]) == 1) { $tarr[0] = "0".$tarr[0]; };
      if (strlen($tarr[1]) == 1) { $tarr[1] = "0".$tarr[1]; };
      $time = "00:".$tarr[0].":".$tarr[1]; 
    } else { 
      if (strlen($tarr[0]) == 1) { $tarr[0] = "0".$tarr[0]; };
      if (strlen($tarr[1]) == 1) { $tarr[1] = "0".$tarr[1]; };
      if (strlen($tarr[2]) == 1) { $tarr[2] = "0".$tarr[2]; };
      $time = $tarr[0].":".$tarr[1].":".$tarr[2]; 
    }
    $query = 'INSERT INTO #__sermon_sermons (`speaker_id`,`series_id`,`sermon_path`,`sermon_title`,`sermon_number`,`sermon_scripture`,`sermon_date`,`sermon_time`,`notes`,`published`,`podcast`)'
          . ' VALUES (\''.(int)$vars[speaker_id].'\',\''.(int)$vars[series_id].'\','.$path.','.$title.','.$nbr.','.$script.','.$date.',\''.$time.'\','.$notes.',\''.(int)$vars[published].'\',\''.(int)$vars[podcast].'\');';
    
	  $db->setQuery($query);
	  if ( !$db->query() ) { die("SQL error" . $db->stderr(true)); }
        
    ?>
    <table border="0">
        <tr>
          <td width ="50">&#160;</td><td colspan="3"><h1><?php echo $lang->fu_upsavedok; ?>!</h1></td>
        </tr>
        <tr>
          <td width ="50">&#160;</td><td colspan="3"><b><?php echo $lang->fu_step; ?> 3 : </b><?php echo $lang->fu_step3; ?></td>
        </tr>
        <tr><td colspan ="4">&#160;</td></tr>
        <tr>
        <td width ="50"></td><td>
            <?php
            $lang = new sermonLang;
            echo "<FORM><INPUT TYPE=\"BUTTON\" VALUE=\"".$lang->fu_another."\" ONCLICK=\"window.location.href='index.php?option=com_sermonspeaker&task=".$config->fu_taskname."&step=1'\"> </FORM>";
            echo "&nbsp;&nbsp;";
            echo fu_logoffbtn();
            ?>
          </td>
        </tr>
    </table>
    <?php
  } else {
    // Form wasn't submitted!
    JHTML::_('behavior.calendar'); 
    JHTML::_('behavior.modal', 'a.modal-button');
    $editor =& JFactory::getEditor(); 
    
    $file = JRequest::getVar('filename');
    $path = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".DS.$config->fu_destdir.DS.$file;
    require_once('id3/getid3/getid3.php');
    $getID3 = new getID3;
    $FileInfo = $getID3->analyze($path);
    getid3_lib::CopyTagsToComments($FileInfo);
      
    $db =& JFactory::getDBO(); 
  	$query = "SELECT name,id FROM #__sermon_speakers";
  	$db->setQuery( $query ); 
  	$speaker_names = $db->loadObjectList();
  	
  	$db =& JFactory::getDBO(); 
  	$query = "SELECT series_title,id FROM #__sermon_series";
  	$db->setQuery( $query ); 
  	$series_title = $db->loadObjectList();
  	
  	if ($config->fu_id3_title != "-") {
      switch ($config->fu_id3_title) {
        case "Artist"  : $id3title = $FileInfo['comments_html']['artist'][0]; break;
        case "Title"   : $id3title = $FileInfo['tags']['id3v2']['title'][0]; break;
        case "Album"   : $id3title = $FileInfo['comments_html']['album'][0]; break;
        case "Track"   : $id3title = $FileInfo['comments_html']['track'][0]; break;
        case "Comment" : $id3title = $FileInfo['comments_html']['comment'][0]; break;
    	}
    } else {
      $id3title="";
    }
    
    if ($config->fu_id3_series != "-") {
      switch ($config->fu_id3_series) {
        case "Artist" : 
          $query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['artist'][0]."';";
          break;
        case "Title" : 
          $query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['tags']['id3v2']['title'][0]."';";
          break;
        case "Album" : 
          $query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['album'][0]."';";
          break;
        case "Track" : 
          $query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['track'][0]."';";
          break;
        case "Comment" : 
          $query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['comment'][0]."';";
          break;
    	}
    	$db->setQuery( $query );
    	$id3series_id = $db->loadRow();
    } else {
      $id3series_id="";
    }
    
    if ($config->fu_id3_ref != "-") {
      switch ($config->fu_id3_ref) {
        case "Artist"  : $id3ref = $FileInfo['comments_html']['artist'][0]; break;
        case "Title"   : $id3ref = $FileInfo['tags']['id3v2']['title'][0]; break;
        case "Album"   : $id3ref = $FileInfo['comments_html']['album'][0]; break;
        case "Track"   : $id3ref = $FileInfo['comments_html']['track'][0]; break;
        case "Comment" : $id3ref = $FileInfo['comments_html']['comment'][0]; break;
    	}
    } else {
      $id3ref="";
    }
    
    if ($config->fu_id3_number != "-") {
      switch ($config->fu_id3_number) {
        case "Artist"  : $id3number = $FileInfo['comments_html']['artist'][0]; break;
        case "Title"   : $id3number = $FileInfo['tags']['id3v2']['title'][0]; break;
        case "Album"   : $id3number = $FileInfo['comments_html']['album'][0]; break;
        case "Track"   : $id3number = $FileInfo['comments_html']['track'][0]; break;
        case "Comment" : $id3number = $FileInfo['comments_html']['comment'][0]; break;
    	}
    } else {
      $id3number="";
    }
    
    if ($config->fu_id3_notes != "-") {
      switch ($config->fu_id3_notes) {
        case "Artist"  : $id3notes = $FileInfo['comments_html']['artist'][0]; break;
        case "Title"   : $id3notes = $FileInfo['tags']['id3v2']['title'][0]; break;
        case "Album"   : $id3notes = $FileInfo['comments_html']['album'][0]; break;
        case "Track"   : $id3notes = $FileInfo['comments_html']['track'][0]; break;
        case "Comment" : $id3notes = $FileInfo['comments_html']['comment'][0]; break;
    	}
    } else {
      $id3notes="";
    }
    
    if ($config->fu_id3_speaker != "-") {
    	$db =& JFactory::getDBO();
      switch ($config->fu_id3_speaker) {
        case "Artist" : 
          $query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['artist'][0]."';";
          break;
        case "Title" : 
          $query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['tags']['id3v2']['title'][0]."';";
          break;
        case "Album" : 
          $query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['album'][0]."';";
          break;
        case "Track" : 
          $query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['track'][0]."';";
          break;
        case "Comment" : 
          $query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['comment'][0]."';";
          break;
      }
    	$db->setQuery( $query );
    	$id3speaker_id = $db->loadRow();
    } else {
      $id3speaker_id="";
    }
    
    $lists['speaker_id'] = JHTML::_('select.genericlist', $speaker_names,'speaker_id','','id','name',$id3speaker_id);
    $lists['series_id'] = JHTML::_('select.genericlist', $series_title,'series_id','','id','series_title',$id3series_id);
    $lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"');
  	$lists['podcast'] = JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"');
    ?>
      <table border="0">
        <tr>
          <td width ="50">&#160;</td><td colspan="3"><h1><?php echo $lang->filename." \"".$file."\" ".$lang->fu_uploadok; ?>!</h1></td>
        </tr>
        <tr>
          <td width ="50">&#160;</td><td colspan="3"><b><?php echo $lang->fu_step; ?> 2 : </b><?php echo $lang->fu_step2; ?></td>
        </tr>
        <tr><td colspan ="4">&#160;</td></tr>
        <tr>
          <td>
            <form name="fu_createsermon" method="post" enctype="multipart/form-data" >
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->sermonTitle; ?> </td> 
    		        <td> &nbsp; <input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $id3title;?>" /> </td>
    		        <td>&#160;</td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->scripture; ?> </td> 
    		        <td> &nbsp; <input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $id3ref;?>" /> </td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->sermon_date; ?> </td> 
    		        <td>  &nbsp; <input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="" /> 
    		        <img class="calendar" src="templates/system/images/calendar.png" alt="calendar" id="showCalendar" /> 
                <script type="text/javascript">
                  Calendar.setup( {
                    inputField  : "sermon_date",
                    ifFormat    : "%Y-%m-%d",
                    button      : "showCalendar"
                  } );
                </script>
                <td><?php echo $lang->fu_date_desc; ?><td>
                </td> 
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->sermonNumber; ?> </td> 
    		        <td> &nbsp; <input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $id3number;?>" /> </td>
    		        <td>&#160;</td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->sermonTime; ?> </td> 
    		        <td> &nbsp; <input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo @$FileInfo['playtime_string'];?>" /> </td>
    		        <td><?php echo $lang->fu_sermonTime_desc; ?></td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->speaker; ?> </td> 
    		        <td> &nbsp; <?php echo $lists['speaker_id']; ?> </td>
    		        <td>&#160;</td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->series; ?> </td> 
    		        <td> &nbsp; <?php echo $lists['series_id']; ?> </td>
    		        <td>&#160;</td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->notes; ?> </td> 
    		        <td> &nbsp; <?php echo $editor->display('notes',$id3notes,'100%','200','40','10');	?> </td>
    		        <td>&#160;</td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->published; ?> </td> 
    		        <td> &nbsp; <?php echo $lists['published']; ?> </td>
    		        <td><?php echo $lang->fu_published_desc; ?></td>
              </tr>
              <tr><td width ="50">&#160;</td>
                <td align="right" class="key"> <?php echo $lang->sermoncast; ?> </td> 
    		        <td> &nbsp; <?php echo $lists['podcast']; ?> </td>
    		        <td><?php echo $lang->fu_sermoncast_desc; ?></td>
              </tr>
              <tr><td colspan ="3">&#160;</td></tr>
              <tr><td width ="50">&#160;</td>
                <td colspan="2"><input type="submit" value=" <?php echo $lang->fu_save; ?> ">&nbsp;
                <input type="reset" value=" <?php echo $lang->fu_reset; ?> "></td>
                <input type="hidden" name="filename" value="<?php echo $file; ?>">
                <input type="hidden" name="submitted" value="true">
                <td>&#160;</td>
              </tr>
            </form>
        </tr>
        <tr>
          <td width ="50"></td><td>
          <br/>&nbsp;<br/>
          <td>
        </tr>
        <tr>
          <td width ="50"></td><td>
            <?php
            echo fu_logoffbtn();
            ?>
          </td>
        </tr>
      </table> 
      <?php
    }
} // end of fu_step2

function fu_step3() {
  fu_security();
  
  $config = new sermonConfig;
  $lang = new sermonLang;
  $reason = JFilterInput::clean(JRequest::getVar('reason'),string);
  ?>
  <table border="0">
    <tr>
      <td width ="50"></td><td><h1><?php echo $lang->fu_failed; ?>!</h1></td>
     </tr>
     <tr>
       <td width ="50"></td><td><b>
         <?php 
         switch ($reason) {
           case "exists" : echo "<b>".$lang->fu_exists."</b>";
           break;
         } ?></b>
       </td>
     </tr>
     <tr>
     <td width ="50"></td><td>
       <?php echo "<br/><a href=\"index.php?option=com_sermonspeaker&task=".$config->fu_taskname."&step=1\">".$lang->fu_another."</a>"; ?>
     </td></tr>
     <tr><td colspan ="4">&#160;</td></tr>
     <tr>
       <td width ="50"></td><td>
       <br/>&nbsp;<br/>
       <td>
     </tr>
     <tr>
       <td width ="50"></td><td>
         <?php echo fu_logoffbtn(); ?>
       </td>
     </tr>
  </table> 
  <?php
} // end of fu_step3

function fu_logout () {
  session_start();
  session_unset();  
  session_destroy();
  
  if (isset($_COOKIE[session_name()])) { 
    setcookie(session_name(), '', time()-42000, '/');
  }
  unset($_SESSION);
  header('Location: index.php?option=com_sermonspeaker');
} // end of fu_logoff

function fu_security () {
  //Check if user is logged on
  $config = new sermonConfig;
  session_start();
  if ($_SESSION["loggedin"] != "loggedin") {
    header('Location: index.php?option=com_sermonspeaker&task='.$config->fu_taskname."&step=9");
    echo "OOOUUUTTT!!!!";
    exit;
  }
}  // end of fu_security

function fu_logoffbtn () {
  $lang = new sermonLang;
  $config = new sermonConfig;
  $str = "<FORM><INPUT TYPE=\"BUTTON\" VALUE=\"".$lang->fu_logout."\" ONCLICK=\"window.location.href='index.php?option=com_sermonspeaker&task=".$config->fu_taskname."&step=0'\"> </FORM>";
  return $str;
} // end of fu_logoffbtn

?>
