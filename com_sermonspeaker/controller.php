<?php
defined('_JEXEC') or die('Restricted access');
global $ss_version;

$ss_version="3.3.1";

jimport('joomla.application.component.controller');

class SermonSpeakerController extends JController { 
	function __construct( $default = array() ) { 
		parent::__construct( $default ); 
		$this->registerTask( 'add' , 'edit' ); 
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'unpublish', 'publish' );
	} // end of __construct
	
	function edit() { 
		global $option; 

		$row =& JTable::getInstance('series', 'Table');
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' ); 
		$id = $cid[0]; 
		$row->load($id); 
		$lists = array();
    
    $db =& JFactory::getDBO(); 
		$query = "SELECT name,id FROM #__sermon_speakers";
		$db->setQuery( $query ); 
		$res = $db->query();
		while ($r=mysql_fetch_array($res)) { $speaker_names[]=$r; }
    
    $db =& JFactory::getDBO(); 
		$query = "SELECT avatar_name,id FROM #__sermon_avatars";
		$db->setQuery( $query ); 
		$avatar_names = $db->loadObjectList();
    
    $db =& JFactory::getDBO();
    $query = "SELECT * FROM #__users ORDER BY name";
    $db->setQuery( $query ); 
		$users = $db->loadObjectList();
		
    $addspeaker = $speaker_names;
    $value = array('name'=>'(none)','id'=>'');
    array_unshift($addspeaker,$value);
		
    $lists['speaker_id'] = JHTML::_('select.genericlist', $speaker_names,'speaker_id','','id','name',$row->speaker_id);
    $lists['speaker2'] = JHTML::_('select.genericlist', $addspeaker,'speaker2','','id','name',$row->speaker2);
    $lists['speaker3'] = JHTML::_('select.genericlist', $addspeaker,'speaker3','','id','name',$row->speaker3);
    $lists['speaker4'] = JHTML::_('select.genericlist', $addspeaker,'speaker4','','id','name',$row->speaker4);
    $lists['speaker5'] = JHTML::_('select.genericlist', $addspeaker,'speaker5','','id','name',$row->speaker5);
    $lists['speaker6'] = JHTML::_('select.genericlist', $addspeaker,'speaker6','','id','name',$row->speaker6);
    $lists['speaker7'] = JHTML::_('select.genericlist', $addspeaker,'speaker7','','id','name',$row->speaker7);
    $lists['speaker8'] = JHTML::_('select.genericlist', $addspeaker,'speaker8','','id','name',$row->speaker8);
    $lists['speaker9'] = JHTML::_('select.genericlist', $addspeaker,'speaker9','','id','name',$row->speaker9);
    $lists['speaker10'] = JHTML::_('select.genericlist', $addspeaker,'speaker10','','id','name',$row->speaker10);
    $lists['speaker11'] = JHTML::_('select.genericlist', $addspeaker,'speaker11','','id','name',$row->speaker11);
    $lists['speaker12'] = JHTML::_('select.genericlist', $addspeaker,'speaker12','','id','name',$row->speaker12);
    $lists['speaker13'] = JHTML::_('select.genericlist', $addspeaker,'speaker13','','id','name',$row->speaker13);
    $lists['speaker14'] = JHTML::_('select.genericlist', $addspeaker,'speaker14','','id','name',$row->speaker14);
    $lists['speaker15'] = JHTML::_('select.genericlist', $addspeaker,'speaker15','','id','name',$row->speaker15);
    $lists['speaker16'] = JHTML::_('select.genericlist', $addspeaker,'speaker16','','id','name',$row->speaker16);
    $lists['speaker17'] = JHTML::_('select.genericlist', $addspeaker,'speaker17','','id','name',$row->speaker17);
    $lists['speaker18'] = JHTML::_('select.genericlist', $addspeaker,'speaker18','','id','name',$row->speaker18);
    $lists['speaker19'] = JHTML::_('select.genericlist', $addspeaker,'speaker19','','id','name',$row->speaker19);
    $lists['speaker20'] = JHTML::_('select.genericlist', $addspeaker,'speaker20','','id','name',$row->speaker20);
    $lists['avatar_id'] = JHTML::_('select.genericlist', $avatar_names,'avatar_id','','id','avatar_name',$row->avatar_id);  
    $lists['created_by'] = JHTML::_('select.genericlist', $users,'created_by','','id','name',$row->created_by);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published); 

		HTML_SermonSpeaker::editSeries($row, $lists, $option); 
	} //end of edit
 
	function save() { 
	  $lang = new sermonLang;
		global $option; 
		
		$row =& JTable::getInstance('series', 'Table');	
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		$row->series_description = JRequest::getVar('series_description','','post','string',JREQUEST_ALLOWRAW);
    if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		switch ($this->_task)        
		{
			case 'apply': 
        $msg = $lang->c_series_saved; 
				$link = 'index.php?option=' .$option.'&task=edit&cid[]='. $row->id; 
				break;
			
			case 'save': 
			default: 
        $msg = $lang->series_saved; 
				$link = 'index.php?option='.$option.'&task=series'; 
				break; 
		}
		 
		$this->setRedirect($link, $msg); 
	} //end of save

	function remove() { 
		global $option; 
		$cid = JRequest::getVar( 'cid', array(), '', 'array' ); 
		$db =& JFactory::getDBO();	 
		if(count($cid)) 
		{ 
			$cids=implode(',',$cid);$cids = implode( ',', $cid ); 
			$query = "DELETE FROM #__sermon_series WHERE id IN ( $cids )"; 
			$db->setQuery( $query );
			if (!$db->query()) { 
				echo "<script> alert('".$db->getErrorMsg()."'); window. history.go(-1); </script>\n"; 
			} 
		}
		$this->setRedirect('index.php?option='.$option); 
	} // end of remove

/*********************************************/
/* SERIES                                    */
/*********************************************/

	function series() { 
		global $option, $mainframe;
		
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
		$limitstart = JRequest::getVar('limitstart', 0); 		
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT count(*) FROM #__sermon_series";
		$db->setQuery( $query ); 
		$total = $db->loadResult(); 
		
		$order = JRequest::getVar('order');
		switch ($order) {
		  default:
      case "title_a"  : 
        $query = "SELECT * FROM #__sermon_series ORDER BY series_title ASC";
        break;
      case "title_d"  : 
        $query = "SELECT * FROM #__sermon_series ORDER BY series_title DESC";
        break;
      case "speaker_a"  : 
        $query = "SELECT a. *, b.name FROM #__sermon_series a, #__sermon_speakers b WHERE a.speaker_id = b.id ORDER BY b.name ASC";
        break;
      case "speaker_d"  : 
        $query = "SELECT a. *, b.name FROM #__sermon_series a, #__sermon_speakers b WHERE a.speaker_id = b.id ORDER BY b.name DESC";
        break;
      case "published_a"  : 
        $query = "SELECT * FROM #__sermon_series ORDER BY published DESC";
        break;
      case "published_d"  : 
        $query = "SELECT * FROM #__sermon_series ORDER BY published ASC";
        break;
    }
		//$query = "SELECT * FROM #__sermon_series";
		$db->setQuery( $query, $limitstart, $limit ); 
		$rows = $db->loadObjectList(); 
		
		if ($db->getErrorNum()) { 
			echo $db->stderr(); 
			return false; 
		} 
		
		jimport('joomla.html.pagination'); 
		$pageNav = new JPagination($total, $limitstart, $limit); 
		HTML_SermonSpeaker::showSeries( $option, $rows, $pageNav );
	} //end of series


/*********************************************/
/* SPEAKERS                                  */
/*********************************************/

  function speakers() { 
		global $option, $mainframe;
		
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
		$limitstart = JRequest::getVar('limitstart', 0); 
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT count(*) FROM #__sermon_speakers";
		$db->setQuery( $query ); 
		$total = $db->loadResult(); 
		
		$query = "SELECT * FROM #__sermon_speakers ORDER BY ordering ASC, name ASC";
		$db->setQuery( $query, $limitstart, $limit ); 
		$rows = $db->loadObjectList(); 
		
		if ($db->getErrorNum()) { 
			echo $db->stderr(); 
			return false; 
		} 
		
		jimport('joomla.html.pagination'); 	
		$pageNav = new JPagination($total, $limitstart, $limit); 
		HTML_SermonSpeaker::showSpeakers( $option, $rows, $pageNav );
	} //end of Speakers
	
	function editSpeakers() {
	 global $option; 

		$row =& JTable::getInstance('speakers', 'Table');
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' ); 
		$id = $cid[0]; 
		$row->load($id); 
		$lists = array();
		
		$db =& JFactory::getDBO();
    $query = "SELECT * FROM #__users ORDER BY name";
    $db->setQuery( $query ); 
		$users = $db->loadObjectList();
		
		$lists['created_by'] = JHTML::_('select.genericlist', $users,'created_by','','id','name',$row->created_by);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		
		HTML_SermonSpeaker::editSpeakers($row, $lists, $option);
	} //end of editSpeakers

  function saveSpeakers() { 
		global $option;
    $database =& JFactory::getDBO(); 
		$row =& JTable::getInstance('speakers', 'Table');
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		$row->intro = JRequest::getVar('intro','','post','string',JREQUEST_ALLOWRAW);
		$row->bio = JRequest::getVar('bio','','post','string',JREQUEST_ALLOWRAW);
	  if (!$row->ordering) {
    	$query = 'SELECT ordering FROM `#__sermon_speakers` order by ordering desc limit 1';
      $database->setQuery( $query ); 
      $row->ordering = ($database->loadResult()+1);
    }
		if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		switch ($this->_task) {
			case 'apply': 
				$msg = 'Changes to Speaker applied'; 
				$link = 'index.php?option='.$option.'&task=editSpeakers&cid[]='. $row->id; 
				break;	
			case 'save': 
			default: 
				$msg = 'Changes to Speaker saved'; 
				$link = 'index.php?option='.$option.'&task=speakers'; 
				break; 
		}	 
		$this->setRedirect($link, $msg); 
	} //end of saveSpeakers
	
	function removeSpeakers() { 
		global $option; 
		$cid = JRequest::getVar( 'cid', array(), '', 'array' ); 
		$db =& JFactory::getDBO();	 
		if(count($cid)) 
		{ 
			$cids=implode(',',$cid);$cids = implode( ',', $cid ); 
			$query = "DELETE FROM #__sermon_speakers WHERE id IN ( $cids )"; 
			$db->setQuery( $query );
			if (!$db->query()) { 
				echo "<script> alert('".$db->getErrorMsg()."'); window. history.go(-1); </script>\n"; 
			} 
		}
		$this->setRedirect('index.php?option='.$option.'&task=speakers'); 
	} // end of removeSpeakers
	
	function movespeakerup() {
    $id = JRequest::getVar('id','post',string);
    $option = JRequest::getVar('option','post',string);
    $database =& JFactory::getDBO();
    
    $query = "SELECT ordering FROM #__sermon_speakers WHERE id=".$id;
    $database->setQuery( $query );
  	$old_ordering = $database->loadResult();
  	if (!$database->query()) {
  	  echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
  	}
  	
  	$query = "UPDATE #__sermon_speakers SET `ordering`='".$old_ordering."' WHERE `ordering`=".($old_ordering-1).";";
    $database->setQuery( $query );
    if (!$database->query()) {
  	  echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
  	}
    $query = "UPDATE #__sermon_speakers SET `ordering`='".($old_ordering-1)."' WHERE `id`='".$id."';";
    $database->setQuery( $query );
    if (!$database->query()) {
  	  echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
  	}
  	
    $this->setRedirect('index.php?option='.$option.'&task=speakers');
  } // end of movespeakerup
  
  function movespeakerdown() {
    $id = JRequest::getVar('id','post',string);
    $option = JRequest::getVar('option','post',string);
    $database =& JFactory::getDBO();
    
    $query = "SELECT ordering FROM #__sermon_speakers WHERE id=".$id.";";
    $database->setQuery( $query );
  	$old_ordering = $database->loadResult();
  	$query = "SELECT ordering FROM #__sermon_speakers ORDER BY ordering desc;";
    $database->setQuery( $query );
  	$highest_ordering = $database->loadResult();
  	
  	$i = $highest_ordering;
  	if ($old_ordering == 0) {
    	while ($i != $old_ordering) {
        $query = "UPDATE #__sermon_speakers SET ordering='".($i+1)."' WHERE ordering=".$i.";";
        $database->setQuery( $query );
        if (!$database->query()) {
    			echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
    		}
      	$i--;
    	}
    } else { 
      $query = "UPDATE #__sermon_speakers SET ordering='".($old_ordering+1)."' WHERE ordering=".$old_ordering.";";
      $database->setQuery( $query );
      if (!$database->query()) {
    	  echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
    	}
    	$query = "UPDATE #__sermon_speakers SET ordering='".$old_ordering."' WHERE ordering=".($old_ordering+1).";";
      $database->setQuery( $query );
      if (!$database->query()) {
    	  echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
    	}
    } // of if ($old_ordering == 0)
  	
  	$query = "UPDATE #__sermon_speakers SET ordering='".($old_ordering+1)."' WHERE id=".$id.";";
    $database->setQuery( $query );
    if (!$database->query()) {
  			echo "<script> alert('".$database->getErrorMsg()."'); </script>\n";
  		}
    $this->setRedirect('index.php?option='.$option.'&task=speakers'); 
  } // end of movespeakerdown


/*********************************************/
/* SERMONS                                   */
/*********************************************/

  function sermons() { 
		global $option, $mainframe;
		
		$order = JRequest::getVar('order');
		switch ($order) {
      case "title_a"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY sermon_title ASC, (sermon_number+0) DESC";
        break;
      case "title_d"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY sermon_title DESC, (sermon_number+0) DESC";
        break;
      case "speaker_a"  : 
        $q = "SELECT a. *, b.name FROM #__sermon_sermons a, #__sermon_speakers b WHERE a.speaker_id = b.id ORDER BY b.name ASC, a.id DESC";
        break;
      case "speaker_d"  : 
        $q = "SELECT a. *, b.name FROM #__sermon_sermons a, #__sermon_speakers b WHERE a.speaker_id = b.id ORDER BY b.name DESC, a.id DESC";
        break;
      case "series_a"  : 
        $q = "SELECT a. *, b.series_title FROM jos_sermon_sermons a, jos_sermon_series b WHERE a.series_id = b.id ORDER BY b.series_title ASC, (a.sermon_number+0) DESC";
        break;
      case "series_d"  : 
        $q = "SELECT a. *, b.series_title FROM jos_sermon_sermons a, jos_sermon_series b WHERE a.series_id = b.id ORDER BY b.series_title DESC, (a.sermon_number+0) DESC";
        break;
      case "date_a"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY sermon_date ASC, (sermon_number+0) ASC";
        break;
      default:
      case "date_d"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY sermon_date DESC, (sermon_number+0) DESC";
        break;
      case "pub_a"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY published ASC, id DESC";
        break;
      case "pub_d"  : 
        $q = "SELECT * FROM #__sermon_sermons ORDER BY published DESC, id DESC";
        break;
    }
    
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
		$limitstart = JRequest::getVar('limitstart',0); 
		$db =& JFactory::getDBO(); 
		$query = "SELECT count(*) FROM #__sermon_sermons";
		$db->setQuery( $query ); 
		$total = $db->loadResult(); 
		 
		$db->setQuery($q, $limitstart, $limit);
		$rows = $db->loadObjectList(); 
		
		if ($db->getErrorNum()) { 
			echo $db->stderr(); 
			return false; 
		} 
    
		jimport('joomla.html.pagination'); 	
		$pageNav = new JPagination($total, $limitstart, $limit); 
		HTML_SermonSpeaker::showSermons($option, $rows, $pageNav);
	} //end of sermons
	
	function editSermons() {
	 global $option; 

		$row =& JTable::getInstance('sermons', 'Table');
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' ); 
		$id = $cid[0]; 
		$row->load($id); 
		$lists = array();
		
		$db =& JFactory::getDBO();
    $query = "SELECT * FROM #__users ORDER BY name";
    $db->setQuery( $query ); 
		$users = $db->loadObjectList();
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT name,id FROM #__sermon_speakers";
		$db->setQuery( $query ); 
		$speaker_names = $db->loadObjectList();
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT series_title,id FROM #__sermon_series";
		$db->setQuery( $query ); 
		$series_title = $db->loadObjectList();

		$lists['speaker_id'] = JHTML::_('select.genericlist', $speaker_names,'speaker_id','','id','name',$row->speaker_id);
		$lists['series_id'] = JHTML::_('select.genericlist', $series_title,'series_id','','id','series_title',$row->series_id);
		$lists['created_by'] = JHTML::_('select.genericlist', $users,'created_by','','id','name',$row->created_by);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		$lists['podcast'] = JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"', $row->podcast);
				
		HTML_SermonSpeaker::editSermons($row, $lists, $option);
	} //end of editSermons

  function saveSermons() {
    $lang = new sermonLang; 
		global $option; 
		$row =& JTable::getInstance('sermons', 'Table');
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		
		$row->notes = JRequest::getVar('notes','','post','string',JREQUEST_ALLOWRAW);
		$sel = JRequest::getVar('sel','post',string);
		if ($sel == 1) {
      $row->sermon_path = JRequest::getVar('sermon_path_txt','post',string);
    } else {
      $row->sermon_path = JRequest::getVar('sermon_path_choice','post',string);
    }

		if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
	
		switch ($this->_task)        
		{
			case 'apply': 
        $msg = $lang->a_sermon_saved; 
				$link = 'index.php?option='.$option.'&task=editSermons&cid[]='. $row->id; 
				break;
			
			case 'save': 
			default: 
        $msg = $lang->sermon_saved; 
				$link = 'index.php?option='.$option.'&task=sermons'; 
				break; 
		}	 
		$this->setRedirect($link, $msg); 
	} //end of saveSermons
	
	function showPopup() {
    HTML_SermonSpeaker::showPopup();
  } // end of showPopup

  function removeSermons() { 
		global $option; 
		
		$cid = JRequest::getVar( 'cid', array(), '', 'array' ); 
		$db =& JFactory::getDBO();	 
		if(count($cid)) 
		{ 
			$cids=implode(',',$cid);$cids = implode( ',', $cid ); 
			$query = "DELETE FROM #__sermon_sermons WHERE id IN ( $cids )"; 
			$db->setQuery( $query );
			if (!$db->query()) { 
				echo "<script> alert('".$db->getErrorMsg()."'); window. history.go(-1); </script>\n"; 
			} 
		}
		$this->setRedirect('index.php?option='.$option.'&task=sermons'); 
	} // end of removeSermons	

/*********************************************/
/* CONFIG                                    */
/*********************************************/
	
	function config() { 
	  global $option;
    //$row =& JTable::getInstance('series', 'Table');
    require_once (JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker/config.sermonspeaker.php');
    require_once (JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker/sermoncastconfig.sermonspeaker.php');
    
    $config = new sermonConfig;
    $sc_config = new sermonCastConfig;
    
		$lists = array();
		$results = array (
		  '0' => array ('value'=>'1','text'=>'1'),
		  '1' => array ('value'=>'5','text'=>'5'),
		  '2' => array ('value'=>'10','text'=>'10'),
		  '3' => array ('value'=>'15','text'=>'15'),
		  '4' => array ('value'=>'20','text'=>'20'),
		  '5' => array ('value'=>'30','text'=>'30'),
		  '6' => array ('value'=>'40','text'=>'40'),
		  '7' => array ('value'=>'50','text'=>'50'),
		  '8' => array ('value'=>'60','text'=>'60'),
		  '9' => array ('value'=>'70','text'=>'70'),
		  '10' => array ('value'=>'80','text'=>'80'),
		  '11' => array ('value'=>'90','text'=>'90'),
		  '12' => array ('value'=>'100','text'=>'100'),
		  '13' => array ('value'=>'150','text'=>'150'),
		);
		$startpage = array (
		  '1' => array ('value'=>'1','text'=>'1'),
		  '2' => array ('value'=>'2','text'=>'2'),
		  '3' => array ('value'=>'3','text'=>'3'),
		  '4' => array ('value'=>'4','text'=>'4'),
		);
		$itcat = array (
      '1' => array ('value'=>'','text'=>'(none)'),
      '2' => array ('value'=>'Education','text'=>'Education'),
      '3' => array ('value'=>'Religion & Spirituality','text'=>'Religion & Spirituality'),
      '4' => array ('value'=>'Religion & Spirituality > Christianity','text'=>'Religion & Spirituality > Christianity'),
      '5' => array ('value'=>'Religion & Spirituality > Judaism','text'=>'Religion & Spirituality > Judaism'),
      '6' => array ('value'=>'Religion & Spirituality > Other','text'=>'Religion & Spirituality > Other'),
      '7' => array ('value'=>'Religion & Spirituality > Spirituality','text'=>'Religion & Spirituality > Spirituality'),
      '8' => array ('value'=>'Music','text'=>'Music'),
      '9' => array ('value'=>'Kids & Family','text'=>'Kids & Family'),
      '10' => array ('value'=>'Sports & Recreation','text'=>'Sports & Recreation'),
      '11' => array ('value'=>'Sports & Recreation > Professional','text'=>'Sports & Recreation > Professional'),
    );
    $it_prefix = array(
      '1' => array ('value'=>'http','text'=>'http'),
      '2' => array ('value'=>'itpc','text'=>'itpc'),
      '3' => array ('value'=>'pcast','text'=>'pcast'),
    );
    $ls_nbr = array (
      '1' => array ('value'=>'1','text'=>'1'),
		  '2' => array ('value'=>'2','text'=>'2'),
		  '3' => array ('value'=>'3','text'=>'3'),
		  '4' => array ('value'=>'4','text'=>'4'),
		  '5' => array ('value'=>'5','text'=>'5'),
		  '6' => array ('value'=>'6','text'=>'6'),
		  '7' => array ('value'=>'7','text'=>'7'),
		  '8' => array ('value'=>'8','text'=>'8'),
		  '9' => array ('value'=>'9','text'=>'9'),
		  '10' => array ('value'=>'10','text'=>'10'),
    );
		$sermonlayout = array (
		  '1' => array ('value'=>'1','text'=>'1'),
		  '2' => array ('value'=>'2','text'=>'2'),
		  '3' => array ('value'=>'3','text'=>'3'),
		);
		$id3cat = array (
		  '-' => array ('value'=>'-','text'=>'-'),
		  'Artist' => array ('value'=>'Artist','text'=>'Artist'),
		  'Title' => array ('value'=>'Title','text'=>'Title'),
		  'Album' => array ('value'=>'Album','text'=>'Album'),
		  'Track' => array ('value'=>'Track','text'=>'Track'),
		  'Comment' => array ('value'=>'Comment','text'=>'Comment'),
		);
		
    //Diplay Settings
    $lists['sermonresults'] = JHTML::_('select.genericlist', $results,'sermonresults','','value','text',$config->sermonresults);
    $lists['limit_speaker'] = JHTML::_('select.booleanlist', 'limit_speaker', 'class="inputbox"', $config->limit_speaker);
    $lists['speaker_intro'] = JHTML::_('select.booleanlist', 'speaker_intro', 'class="inputbox"', $config->speaker_intro);
    $lists['autostart'] = JHTML::_('select.booleanlist', 'autostart', 'class="inputbox"', $config->autostart);
    $lists['startpage'] = JHTML::_('select.genericlist', $startpage,'startpage','','value','text',$config->startpage);
    $lists['sermon_number'] = JHTML::_('select.booleanlist', 'sermon_number', 'class="inputbox"', $config->client_col_sermon_number);
    $lists['sermon_scripture_reference'] = JHTML::_('select.booleanlist', 'sermon_scripture_reference', 'class="inputbox"', $config->client_col_sermon_scripture_reference);
    $lists['sermon_date'] = JHTML::_('select.booleanlist', 'sermon_date', 'class="inputbox"', $config->client_col_sermon_date);
    $lists['sermon_time'] = JHTML::_('select.booleanlist', 'sermon_time', 'class="inputbox"', $config->client_col_sermon_time);
    $lists['sermon_notes'] = JHTML::_('select.booleanlist', 'sermon_notes', 'class="inputbox"', $config->client_col_sermon_notes);
    $lists['player'] = JHTML::_('select.booleanlist', 'player', 'class="inputbox"', $config->client_col_player);
    $lists['search'] = JHTML::_('select.booleanlist', 'search', 'class="inputbox"', $config->search);
    $lists['dateformat'] = JHTML::_('select.booleanlist', 'dateformat', 'class="inputbox"', $config->date_format);
    $lists['videoplayerwidth'] = JHTML::_('select.booleanlist', 'videoplayerwidth', 'class="inputbox"', $config->mp_width);
    $lists['videoplayerheight'] = JHTML::_('select.booleanlist', 'videoplayerheight', 'class="inputbox"', $config->mp_height);
    $lists['sermonlayout'] = JHTML::_('select.genericlist', $sermonlayout,'sermonlayout','','value','text',$config->sermonlayout);
    $lists['allow_ussp'] = JHTML::_('select.booleanlist', 'allow_ussp', 'class="inputbox"', $config->allow_ussp);
    $lists['sermon_addfile'] = JHTML::_('select.booleanlist', 'sermon_addfile', 'class="inputbox"', $config->client_col_sermon_addfile);
    $lists['popup_player'] = JHTML::_('select.booleanlist', 'popup_player', 'class="inputbox"', $config->popup_player);
    $lists['dl_button'] = JHTML::_('select.booleanlist', 'dl_button', 'class="inputbox"', $config->dl_button);
    $lists['hide_dl'] = JHTML::_('select.booleanlist', 'hide_dl', 'class="inputbox"', $config->hide_dl);
    
    //Statistics 
		$lists['track_speaker'] = JHTML::_('select.booleanlist', 'track_speaker', 'class="inputbox"', $config->track_speaker);
    $lists['track_series'] = JHTML::_('select.booleanlist', 'track_series', 'class="inputbox"', $config->track_series);
    $lists['track_sermon'] = JHTML::_('select.booleanlist', 'track_sermon', 'class="inputbox"', $config->track_sermon); 
    
    //Sermoncast Settings
    $lists['mod_showpcast'] = JHTML::_('select.booleanlist', 'mod_showpcast', 'class="inputbox"', $sc_config->mod_showpcast);
    $lists['mod_showplink'] = JHTML::_('select.booleanlist', 'mod_showplink', 'class="inputbox"', $sc_config->mod_showplink);
    $lists['cache'] = JHTML::_('select.booleanlist', 'cache', 'class="inputbox"', $sc_config->cache);
    $lists['limittext'] = JHTML::_('select.booleanlist', 'limittext', 'class="inputbox"', $sc_config->limit_text);
    $lists['itcat1'] = JHTML::_('select.genericlist', $itcat,'itcat1','','value','text',$sc_config->itCategory1);
    $lists['itcat2'] = JHTML::_('select.genericlist', $itcat,'itcat2','','value','text',$sc_config->itCategory2);
    $lists['itcat3'] = JHTML::_('select.genericlist', $itcat,'itcat3','','value','text',$sc_config->itCategory3);
    $lists['it_prefix'] = JHTML::_('select.genericlist', $it_prefix,'it_prefix','','value','text',$sc_config->it_prefix);
    $lists['ls_nbr'] = JHTML::_('select.genericlist', $ls_nbr,'ls_nbr_latest','','value','text',$config->ls_nbr_latest);
    $lists['ls_show_mouseover'] = JHTML::_('select.booleanlist', 'ls_show_mouseover', 'class="inputbox"', $config->ls_show_mouseover);
    $lists['ls_show_mo_speaker'] = JHTML::_('select.booleanlist', 'ls_show_mo_speaker', 'class="inputbox"', $config->ls_show_mo_speaker);
    $lists['ls_show_mo_series'] = JHTML::_('select.booleanlist', 'ls_show_mo_series', 'class="inputbox"', $config->ls_show_mo_series);
    $lists['ls_show_mo_date'] = JHTML::_('select.booleanlist', 'ls_show_mo_date', 'class="inputbox"', $config->ls_show_mo_date);
    $lists['mod_showhelp'] = JHTML::_('select.booleanlist', 'mod_showhelp', 'class="inputbox"', $sc_config->mod_showhelp);
    
    //Frontend Uploading
    $lists['fu_enable'] = JHTML::_('select.booleanlist', 'fu_enable', 'class="inputbox"', $config->fu_enable);
    $lists['fu_id3_title'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_title','','value','text',$config->fu_id3_title);
    $lists['fu_id3_ref'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_ref','','value','text',$config->fu_id3_ref);
    $lists['fu_id3_number'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_number','','value','text',$config->fu_id3_number);
    $lists['fu_id3_notes'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_notes','','value','text',$config->fu_id3_notes);
    $lists['fu_id3_speaker'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_speaker','','value','text',$config->fu_id3_speaker);
    $lists['fu_id3_series'] = JHTML::_('select.genericlist', $id3cat,'fu_id3_series','','value','text',$config->fu_id3_series);
    
		HTML_SermonSpeaker::showConfig($row, $lists, $option); 
	} //end of edit
	
	
	function saveConfig() {
    $lang = new sermonLang;
    global $option;
    global $ss_version;
    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.sermonspeaker.php');
    $config = new sermonConfig;
    $fu_pwd = $config->fu_pwd;
    $configfile = "components/com_sermonspeaker/config.sermonspeaker.php";
    $permission = is_writable($configfile);
    if (!$permission) {
	   $this->setRedirect('index.php?option='.$option.'&task=config',$lang->err_unwritable);
	   return;
    }
    $sermonresults = JRequest::getVar('sermonresults','post',string);
    $limit_speaker = JRequest::getVar('limit_speaker','post',string);
    $track_speaker = JRequest::getVar('track_speaker','post',string);
    $track_series = JRequest::getVar('track_series','post',string);
    $track_sermon = JRequest::getVar('track_sermon','post',string);
    $speaker_intro = JRequest::getVar('speaker_intro','post',string);
    $autostart = JRequest::getVar('autostart','post',string);
    $client_col_sermon_number = JRequest::getVar('sermon_number','post',string);
    $client_col_sermon_scripture_reference = JRequest::getVar('sermon_scripture_reference','post',string);
    $client_col_sermon_date = JRequest::getVar('sermon_date','post',string);
    $client_col_sermon_time = JRequest::getVar('sermon_time','post',string);
    $client_col_sermon_notes = JRequest::getVar('sermon_notes','post',string);
    $client_col_player = JRequest::getVar('player','post',string);
    $search = JRequest::getVar('search','post',string);
    $date_format = JRequest::getVar('dateformat','post',string);
    $startpage = JRequest::getVar('startpage','post',string);
    $ls_nbr_latest = JRequest::getVar('ls_nbr_latest','post',string);
    $mp_width = JRequest::getVar('mp_width','post',string);
    $mp_height = JRequest::getVar('mp_height','post',string);
    $ls_show_mouseover = JRequest::getVar('ls_show_mouseover','post',string);
    $ls_show_mo_speaker = JRequest::getVar('ls_show_mo_speaker','post',string);
    $ls_show_mo_series = JRequest::getVar('ls_show_mo_series','post',string);
    $ls_show_mo_date = JRequest::getVar('ls_show_mo_date','post',string);
    $color1 = JRequest::getVar('color1','post',string);
    $color2 = JRequest::getVar('color2','post',string);
    $sermonlayout = JRequest::getVar('sermonlayout','post',string);
    $ga = JRequest::getVar('ga','post',string);
    $allow_ussp = JRequest::getVar('allow_ussp','post',string);
    $client_col_sermon_addfile = JRequest::getVar('sermon_addfile','post',string);
    $popup_player = JRequest::getVar('popup_player','post',string);
    $popup_color = JRequest::getVar('popup_color','post',string);
    $dl_button = JRequest::getVar('dl_button','post',string);
    $popup_height = JRequest::getVar('popup_height','post',string);
    $fu_enable = JRequest::getVar('fu_enable','post',string);
    $fu_taskname = JRequest::getVar('fu_taskname','post',string);
    $fu_destdir = JRequest::getVar('fu_destdir','post',string);
    $fu_pwd1 = JFilterInput::clean(JRequest::getVar('fu_pwd1'),string);
    $fu_pwd2 = JFilterInput::clean(JRequest::getVar('fu_pwd2'),string);
    $hide_dl = JRequest::getVar('hide_dl','post',string);
    if ($fu_pwd1 != '' && $fu_pwd2 != '') {
      if ($fu_pwd1 == $fu_pwd2) {
        if (strlen($fu_pwd1) > 5) {
          $salt = substr(md5(uniqid(rand(), true)), 0, 9);
          $fu_pwd = sha1($fu_pwd1.$salt).":".$salt;
          $pwd_out = $lang->pwd_changed;
        } else {
          $pwd_out = $lang->pwd_short;
        }     
      } else {
        $pwd_out = $lang->pwd_match;
      }
    }
    $fu_id3_title = JRequest::getVar('fu_id3_title','post',string);
    $fu_id3_speaker = JRequest::getVar('fu_id3_speaker','post',string);
    $fu_id3_series = JRequest::getVar('fu_id3_series','post',string);
    $fu_id3_ref = JRequest::getVar('fu_id3_ref','post',string);
    $fu_id3_number = JRequest::getVar('fu_id3_number','post',string);
    $fu_id3_notes = JRequest::getVar('fu_id3_notes','post',string);
    $config = "<?php \n";
    $config .= "/*\n";
    $config .= "* File: config.sermonspeaker.php\n";
    $config .= "*  @ speaker - A SermonSpeaker Component\n";
    $config .= "*  @ Copyright 2006 - 2009 by Steve Shiflett & Martin Hess\n";
    $config .= "*  @ Website - http://joomlacode.org/gf/project/sermon_speaker/\n";
    $config .= "*/\n";
    $config .= "\n";
    $config .= "defined( '_JEXEC' ) or die( 'Restricted access' );\n";
    $config .= "\n";
    $config .= "class sermonConfig {\n";
    $config .= "var \$ss_version = \"".$ss_version."\" ;\n";
    $config .= "var \$sermonresults = \"".$sermonresults."\" ;\n";
    $config .= "var \$limit_speaker = \"".$limit_speaker."\" ;\n";
    $config .= "var \$speaker_intro = \"".$speaker_intro."\" ;\n";
    $config .= "var \$client_col_sermon_number = \"".$client_col_sermon_number."\" ;\n";
    $config .= "var \$client_col_sermon_scripture_reference = \"".$client_col_sermon_scripture_reference."\" ;\n";
    $config .= "var \$client_col_sermon_date = \"".$client_col_sermon_date."\" ;\n";
    $config .= "var \$client_col_sermon_time = \"".$client_col_sermon_time."\" ;\n";
    $config .= "var \$client_col_sermon_notes = \"".$client_col_sermon_notes."\" ;\n";
    $config .= "var \$client_col_player = \"".$client_col_player."\" ;\n";
    $config .= "var \$track_speaker = \"".$track_speaker."\" ;\n";
    $config .= "var \$track_series = \"".$track_series."\" ;\n";
    $config .= "var \$track_sermon = \"".$track_sermon."\" ;\n";
    $config .= "var \$search = \"".$search."\" ;\n";
    $config .= "var \$date_format = \"".$date_format."\" ;\n";
    $config .= "var \$startpage = \"".$startpage."\" ;\n";
    $config .= "var \$ls_nbr_latest = \"".$ls_nbr_latest."\" ;\n";
    $config .= "var \$mp_width = \"".$mp_width."\" ;\n";
    $config .= "var \$mp_height = \"".$mp_height."\" ;\n";
    $config .= "var \$ls_show_mouseover = \"".$ls_show_mouseover."\" ;\n";
    $config .= "var \$ls_show_mo_speaker = \"".$ls_show_mo_speaker."\" ;\n";
    $config .= "var \$ls_show_mo_series = \"".$ls_show_mo_series."\" ;\n";
    $config .= "var \$ls_show_mo_date = \"".$ls_show_mo_date."\" ;\n";
    $config .= "var \$color1 = \"".$color1."\" ;\n";
    $config .= "var \$color2 = \"".$color2."\" ;\n";
    $config .= "var \$sermonlayout = \"".$sermonlayout."\" ;\n";
    $config .= "var \$ga = \"".$ga."\" ;\n";
    $config .= "var \$allow_ussp = \"".$allow_ussp."\" ;\n";
    $config .= "var \$autostart = \"".$autostart."\" ;\n";
    $config .= "var \$client_col_sermon_addfile = \"".$client_col_sermon_addfile."\" ;\n";
    $config .= "var \$popup_player = \"".$popup_player."\" ;\n";
    $config .= "var \$popup_color = \"".$popup_color."\" ;\n";
    $config .= "var \$dl_button = \"".$dl_button."\" ;\n";
    $config .= "var \$popup_height = \"".$popup_height."\" ;\n";
    $config .= "var \$fu_enable = \"".$fu_enable."\" ;\n";
    $config .= "var \$fu_taskname = \"".$fu_taskname."\" ;\n";
    $config .= "var \$fu_destdir = \"".$fu_destdir."\" ;\n";
    $config .= "var \$fu_pwd = \"".$fu_pwd."\" ;\n";
    $config .= "var \$fu_id3_title = \"".$fu_id3_title."\" ;\n";
    $config .= "var \$fu_id3_speaker = \"".$fu_id3_speaker."\" ;\n";
    $config .= "var \$fu_id3_series = \"".$fu_id3_series."\" ;\n";
    $config .= "var \$fu_id3_ref = \"".$fu_id3_ref."\" ;\n";
    $config .= "var \$fu_id3_number = \"".$fu_id3_number."\" ;\n";
    $config .= "var \$fu_id3_notes = \"".$fu_id3_notes."\" ;\n";
    $config .= "var \$hide_dl = \"".$hide_dl."\" ;\n";
    $config .= "} \n?";
    $config .= ">";
    if ($fp = fopen("$configfile", "w")) {
	   fputs($fp, $config, strlen($config));
	   fclose ($fp);
    }
    //Sermoncast Config
    $cf = "components/com_sermonspeaker/sermoncastconfig.sermonspeaker.php";
    $permission = is_writable($configfile);
    if (!$permission) {
	   $this->setRedirect('index.php?option='.$option.'&task=config',$lang->err_sc_unwritable);
	   return;
    }
    $cache = JRequest::getVar('cache','post',string);
    $cache_time = JRequest::getVar('cache_time','post',string);
    $mimetype = JRequest::getVar('mimetype','post',string);
    $encoding = JRequest::getVar('encoding','post',string);
    $count = JRequest::getVar('count','post',string);
    $title = JRequest::getVar('title','post',string);
    $description = JRequest::getVar('description','post',string);
    $copyright = JRequest::getVar('copyright','post',string);
    $limit_text = JRequest::getVar('limittext','post',string);
    $text_length = JRequest::getVar('text_length','post',string);
    $itAuthor = JRequest::getVar('itAuthor','post',string);
    $itImage = JRequest::getVar('itImage','post',string);
    $itCategory1 = JRequest::getVar('itcat1','post',string);
    $itCategory2 = JRequest::getVar('itcat2','post',string);
    $itCategory3 = JRequest::getVar('itcat3','post',string);
    $itKeywords = JRequest::getVar('itKeywords','post',string);
    $itOwnerEmail = JRequest::getVar('itOwnerEmail','post',string);
    $itOwnerName = JRequest::getVar('itOwnerName','post',string);
    $itSubtitle = JRequest::getVar('itSubtitle','post',string);
    $mod_text = JRequest::getVar('mod_text','post',string);
    $mod_showpcast = JRequest::getVar('mod_showpcast','post',string);
    $mod_showplink = JRequest::getVar('mod_showplink','post',string);
    $it_prefix = JRequest::getVar('it_prefix','post',string);
    $itLanguage = JRequest::getVar('itLanguage','post',string);
    $itRedirect = JRequest::getVar('itRedirect','post',string);
    $mod_rss20 = JRequest::getVar('mod_rss20','post',string);
    $mod_rss20_image = JRequest::getVar('mod_rss20_image','post',string);
    $mod_showhelp = JRequest::getVar('mod_showhelp','post',string);
    $sc_helpeditor = JRequest::getVar('sc_helpeditor','','post',string,JREQUEST_ALLOWRAW);
    $sc_helpwidth = JRequest::getVar('sc_helpwidth','post',string);
    $sc_helpheight = JRequest::getVar('sc_helpheight','post',string);
    $config = "<?php \n";
    $config .= "/*\n";
    $config .= "* File: sermoncastconfig.sermonspeaker.php\n";
    $config .= "*  @ speaker - A SermonSpeaker Component\n";
    $config .= "*  @ Copyright 2006 - 2009 by Steve Shiflett & Martin Hess\n";
    $config .= "*  @ Website - http://joomlacode.org/gf/project/sermon_speaker/\n";
    $config .= "*/\n";
    $config .= "\n";
    $config .= "defined( '_JEXEC' ) or die( 'Restricted access' );\n";
    $config .= "\n";
    $config .= "class sermonCastConfig {\n";
    $config .= "var \$cache = \"".$cache."\" ;\n";
    $config .= "var \$cache_time = \"".$cache_time."\" ;\n";
    $config .= "var \$mimetype = \"".$mimetype."\" ;\n";
    $config .= "var \$encoding = \"".$encoding."\" ;\n";
    $config .= "var \$count = \"".$count."\" ;\n";
    $config .= "var \$title = \"".$title."\" ;\n";
    $config .= "var \$description = \"".$description."\" ;\n";
    $config .= "var \$copyright = \"".$copyright."\" ;\n";
    $config .= "var \$limit_text = \"".$limit_text."\" ;\n";
    $config .= "var \$text_length = \"".$text_length."\" ;\n";
    $config .= "var \$itAuthor = \"".$itAuthor."\" ;\n";
    $config .= "var \$itImage = \"".$itImage."\" ;\n";
    $config .= "var \$itCategory1 = \"".$itCategory1."\" ;\n";
    $config .= "var \$itCategory2 = \"".$itCategory2."\" ;\n";
    $config .= "var \$itCategory3 = \"".$itCategory3."\" ;\n";
    $config .= "var \$itKeywords = \"".$itKeywords."\" ;\n";
    $config .= "var \$itOwnerEmail = \"".$itOwnerEmail."\" ;\n";
    $config .= "var \$itOwnerName = \"".$itOwnerName."\" ;\n";
    $config .= "var \$itSubtitle = \"".$itSubtitle."\" ;\n";
    $config .= "var \$mod_text = \"".$mod_text."\" ;\n";
    $config .= "var \$mod_showpcast = \"".$mod_showpcast."\" ;\n";
    $config .= "var \$mod_showplink = \"".$mod_showplink."\" ;\n";
    //$config .= "var \$mod_rss20 = \"".$mod_rss20."\" ;\n";
    $config .= "var \$mod_rss20 = \"1\" ;\n";
    //$config .= "var \$mod_rss20_image = \"".$mod_rss20_image."\" ;\n";
    $config .= "var \$mod_rss20_image = \" \" ;\n";
    $config .= "var \$it_prefix = \"".$it_prefix."\" ;\n";
    $config .= "var \$itLanguage = \"".$itLanguage."\" ;\n";
    $config .= "var \$itRedirect = \"".$itRedirect."\" ;\n";
    $config .= "var \$mod_showhelp = \"".$mod_showhelp."\" ;\n";
    $config .= "var \$sc_helpeditor = '".$sc_helpeditor."' ;\n";
    $config .= "var \$sc_helpwidth = \"".$sc_helpwidth."\" ;\n";
    $config .= "var \$sc_helpheight = \"".$sc_helpheight."\" ;\n";
    $config .= "} \n?";
    $config .= ">";
    if ($fp = fopen("$cf", "w")) {
	   fputs($fp, $config, strlen($config));
	   fclose ($fp);
    }
    $this->setRedirect('index.php?option='.$option.'&task=config', $lang->saved.$pwd_out);
  } //end of saveConfig
	
	function publish() { 
		global $option;

		$cid = JRequest::getVar('cid',array(),'','array'); 
		$act = JRequest::getVar('act',''); 
		
		if( $this->_task == 'publish') 
		{ $publish = 1; } 
		else 
		{ $publish = 0; }
		
		switch ($act) 
    {
		  case 'avatars':   
        $reviewTable =& JTable::getInstance('avatars','Table');
		    $task="avatars";
		    break;
		  case 'series':    
        $reviewTable =& JTable::getInstance('series','Table');
		    $task="series";
		    break;
      case 'sermons':   
        $reviewTable =& JTable::getInstance('sermons','Table');
        $task="sermons";
		    break;
      case 'speakers':  
        $reviewTable =& JTable::getInstance('speakers','Table');
        $task="speakers";
		    break;                                                
		} 
		$reviewTable->publish($cid, $publish); 
		$this->setRedirect('index.php?option='.$option.'&task='.$task ); 
	} // end of publish
	
	
/*********************************************/
/* HELP                                      */
/*********************************************/
	function help() {  
    HTML_SermonSpeaker::showHelp();
  } // end of help
	
/*********************************************/
/* Main                                      */
/*********************************************/
	function main() {
	  global $option;
    HTML_SermonSpeaker::showmain($option, $config);
  } // end of help

/*********************************************/
/* Media Manager                             */
/*********************************************/	
  
	function media() {
  	$listdir = JRequest::getVar('listdir','');
  	// get list of directories
  	$imgFiles = SermonSpeakerController::recursive_listdir( JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media" );
    $images = array();
  	$folders = array();
  	$folders[] = array("name"=>"(home)","path"=>"/");
  	foreach ($imgFiles as $file) {
  	  $name = substr($file,strlen(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media"));
  	  // Convert backslashed to forwardslashes for compatibility to function imageStyle in admin-html
  	  $name = ereg_replace('\\\\','/',$name);
  	  $folders[] = array("name"=>$name,"path"=>$name);
  	}
  	if (is_array($folders)) {
  		sort( $folders );
  	}
  	echo $folder_list;
  	// create folder selectlist
  	$dirPath = JHTML::_('select.genericlist', $folders, 'dirPath', "class=\"inputbox\" size=\"1\" onchange=\"goUpDir()\" ",'path','name',"/" );
  	HTML_SermonSpeaker::showMedia($dirPath,$listdir);
  } // end of showMedia

  function listImages() {
    $listdir = JRequest::getVar('listdir','');
  
    // get list of images
    $d = @dir(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir);
    if($d) {
    	$images = array();
    	$folders = array();
    	$docs = array();
    
    	while (false !== ($entry = $d->read())) {
    		$img_file = $entry;
    		if(is_file(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
    			if (eregi( "bmp|gif|jpg|png", $img_file )) {
    				$image_info = @getimagesize(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$img_file);
    				$file_details['file'] = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$img_file;
    				$file_details['img_info'] = $image_info;
    				$file_details['size'] = filesize(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$img_file);
    				$images[$entry] = $file_details;
    			} else {
    				// file is document
    				$docs[$entry] = $img_file;
    			}
    		} else if(is_dir(JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
    			$folders[$entry] = $img_file;
    		}
    	} // of while
    	$d->close();
    
    	HTML_SermonSpeaker::imageStyle($listdir);
    
    	if(count($images) > 0 || count($folders) > 0 || count($docs) > 0) {
        //now sort the folders and images by name.
        ksort($images);
        ksort($folders);
        ksort($docs);
        
        HTML_SermonSpeaker::draw_table_header();
        
        for($i=0; $i<count($folders); $i++) {
         $folder_name = key($folders);
         HTML_SermonSpeaker::show_dir(DS.$folders[$folder_name], $folder_name,$listdir);
         next($folders);
        }
        
        for($i=0; $i<count($docs); $i++) {
         $doc_name = key($docs);
         $iconfile= JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media/images/".substr($doc_name,-3)."_16.png";
        if (file_exists($iconfile))	{
          $icon = "components/com_media/images/".(substr($doc_name,-3))."_16.png"	; }
        else {
          $icon = "components/com_media/images/con_info.png";
        }
        HTML_SermonSpeaker::show_doc($docs[$doc_name], $listdir, $icon);
        next($docs);
        } // of for
    	   
        for($i=0; $i<count($images); $i++) {
         $image_name = key($images);
         HTML_SermonSpeaker::show_image($images[$image_name]['file'], $image_name, $images[$image_name]['img_info'], $images[$image_name]['size'],$listdir);
         next($images);
        } // of for
    		HTML_SermonSpeaker::draw_table_footer();
    	} else {
    		HTML_SermonSpeaker::draw_no_results();
    	}
    } else {
  	//HTML_SermonSpeaker::draw_no_dir();
    HTML_SermonSpeaker::draw_no_dir($listdir);
    }
  } // end of listImages
  
  
  function rm_all_dir($dir) {
  	//$dir = dir_name($dir);
  	//echo "OPEN:".$dir.'<Br>';
    if(is_dir($dir)) {
    	$d = @dir($dir);
    
    	while (false !== ($entry = $d->read())) {
    	  //echo "#".$entry.'<br>';
    	  if($entry != '.' && $entry != '..') {
    		  $node = $dir.'/'.$entry;
    		  //echo "NODE:".$node;
    		  if(is_file($node)) {
    		    //echo " - is file<br>";
    		    unlink($node);
    		  }
    		  else if(is_dir($node)) {
    		    //echo " -	is Dir<br>";
    		    rm_all_dir($node);
    		  }
    	  }
    	} // of while
    	$d->close();
    	rmdir($dir);
    }
  } // of rm_all_dir
  
  function upload(){
  	if(isset($_FILES['upload']) && is_array($_FILES['upload']) && isset($_POST['dirPath'])) {
  		$dirPathPost = $_POST['dirPath'];
  		if(strlen($dirPathPost) > 0) {
  			if(substr($dirPathPost,0,1)=='/') {
  				$IMG_ROOT .= $dirPathPost;
  			} else {
  				$IMG_ROOT = $dirPathPost;
  			}
  		}
  		if(strrpos($IMG_ROOT, '/')!= strlen($IMG_ROOT)-1)  {
  			$IMG_ROOT .= '/';
  		}
  		if ($_POST['dirPath']== '/avatars') {
  			SermonSpeakerController::insert_avatar_info($_FILES['upload']);
  		}
  		SermonSpeakerController::do_upload( $_FILES['upload'], JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media/".$dirPathPost.'/');
  		$this->setRedirect('index.php?option=com_sermonspeaker&task=media&listdir='.$dirPathPost);
  	}
  } // end of upload
  
  function insert_avatar_info($fa){
    $lang = new sermonLang;
  	$database =& JFactory::getDBO();
		$file_name = strtolower($fa['name']);
		$type = substr($fa['type'],0,5);  // should = 'image'
		$name = substr($file_name,0,strlen($file_name)-4);
		$location = "/components/com_sermonspeaker/media/avatars/".$file_name;
		$ext = substr($file_name, strlen($file_name)-3, strlen($file_name));
		if ($ext == 'gif'|| $ext == 'jpg' || $ext == 'png'||$ext=='bmp') {
			$query = "insert into   #__sermon_avatars (avatar_name, avatar_location)values('$name','$location')";
			$database->setQuery( $query );
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			}		
		} else {
			$this->setRedirect( "index2.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->err_filetype );
		}
	return;
  } // end of insert_avatar_info
  
  function do_upload($file, $dest_dir) {
    $lang = new sermonLang;
    global $clearUploads;
    
    jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);
    
    if (isset($file['name'])) {
    	if (file_exists($dest_dir.strtolower($file['name']))) {
    		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->err_fileexists );
    	}
    	
      if ((strcasecmp(substr($file['name'],-4),".gif")) && (strcasecmp(substr($file['name'],-4),".jpg")) && (strcasecmp(substr($file['name'],-4),".png")) && (strcasecmp(substr($file['name'],-4),".bmp")) &&(strcasecmp(substr($file['name'],-4),".mp3"))) {
    		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->err_upload_type );
    	}
  
    	//if (!JFile::upload($file['tmp_name'], $dest_dir.strtolower($file['name']))) {
    	if (!JFile::upload($file['tmp_name'], $dest_dir.strtolower(SermonSpeakerController::filename_safe($file['name'])))) {
    	  $this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->err_upload_failed );
    	} else {
        if (strnatcasecmp(substr($file['name'],-4),".mp3") == 0 ){
    			$loc = "/components/com_sermonspeaker/media/";
    			$this->setRedirect("index.php?option=com_sermonspeaker&task=newsermonupload&uploadedfile=".$loc.$file['name']);
    		} else {
    		  $this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->upload_complete );
    	  }
      }
      $clearUploads = true;
    } else {
			$mainframe->redirect('index.php', $lang->err_invalid_req, 'error');
		}
  } // end of do_upload
  
  function deleteFile() {
    jimport('joomla.client.helper');
    jimport('joomla.filesystem.file');
		JClientHelper::setCredentialsFromRequest('ftp');
		
    $listdir = JRequest::getVar('listdir','');
    $delfile = JRequest::getVar('delFile','');
    $listdir = JRequest::getVar('listdir','');
  	$del_image = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$delfile;
    
    if (is_file($del_image)) {
		  $ret |= !JFile::delete($del_image);
		}
  	$dir = substr($listdir,1,8);
  	SermonSpeakerController::remove_associated_records("./components/com_sermonspeaker/media".$listdir."/".$delfile, $dir);
  	$this->setRedirect('index.php?option=com_sermonspeaker&task=media&listdir='.$listdir); 
  } // end of deleteFile
  
  
  function remove_associated_records($del_reference,$dir){
  	$database =& JFactory::getDBO();
  	if( $dir == 'avatars'){
  		$query = "DELETE FROM #__sermon_avatars WHERE avatar_location = \"".substr($del_reference,1,strlen($del_reference))."\""; // chop off that '.'
  		$database->setQuery( $query );
  		if (!$database->query()) {
  			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  		}
  	} else {
  		$query = "DELETE FROM #__sermon_sermons WHERE sermon_path = \"$del_reference\"";
  		$database->setQuery( $query );
  		if (!$database->query()) {
  			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
  		}
  	}
  } // end of remove_associated_records
  
  function create_folder() {
   $lang = new sermonLang;
   jimport('joomla.client.helper');
   jimport('joomla.filesystem.file');
   jimport('joomla.filesystem.folder');
	 JClientHelper::setCredentialsFromRequest('ftp');
	 
	 $folder_name = JRequest::getVar('foldername','','post');
   $dirPath = JRequest::getVar('dirPath');
   $listdir = JRequest::getVar('listdir','');
   if(strlen($folder_name) >0) {
  	 if (eregi("[^0-9a-zA-Z_/]", $folder_name)) {
   		 $this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], $lang->err_char );
  	 }
	 }
	 $path = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$dirPath.DS.$folder_name;
	 
	 if (!is_dir($path) && !is_file($path)) {
  	 jimport('joomla.filesystem.*');
  	 JFolder::create($path);
  	 JFile::write($path.DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
   }
	 
	 $this->setRedirect('index.php?option=com_sermonspeaker&task=media&listdir='.$listdir);
  } // end of create_folder
  
  function deletefolder() {
    jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
    $listdir = JRequest::getVar('listdir','');
    $delFolder = JRequest::getVar('delFolder','');
  	$del_folder = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.$delFolder;
    if (!is_file($del_folder) && is_dir($del_folder)) {
      JFolder::delete($del_folder);
    }
  	$this->setRedirect('index.php?option=com_sermonspeaker&task=media&listdir='.$listdir); 
  } // end of delete_folder
  
  
  function recursive_listdir($base) {
    static $filelist = array();
    static $dirlist = array();
    if(is_dir($base)) {
       $dh = opendir($base);
       while (false !== ($dir = readdir($dh))) {
         if (is_dir($base ."/". $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') {
           $subbase = $base .DS. $dir;
           $dirlist[] = $subbase;
           $subdirlist = SermonSpeakerController::recursive_listdir($subbase);
         } // of if
       } // of while
      closedir($dh);
    } // of if
    return $dirlist;
  } // end of recursive_listdir
  
  
/*********************************************/
/* Statistic                                 */
/*********************************************/
  function stats() {
  	global $option; 
  	
  	$database =& JFactory::getDBO();
  
  	$query = "SELECT * FROM #__sermon_speakers ORDER BY id";
  	$database->setQuery( $query );
  	$rows1 = $database->loadObjectList();
  
  	$query = "SELECT * FROM #__sermon_series ORDER BY id";
  	$database->setQuery( $query );
  	$rows2 = $database->loadObjectList();
  
  	$query = "SELECT * FROM #__sermon_sermons ORDER BY id";
  	$database->setQuery( $query );
  	$rows3 = $database->loadObjectList();
  
  	HTML_SermonSpeaker::showstats( $rows1, $rows2, $rows3, $option, $config );
  } // end of showstats

function resetcount() {
	global $option; 
  
  $database =& JFactory::getDBO();
  $id = JRequest::getVar('id', 0);
  $table = JRequest::getVar('table', 0);
  
  
	$query = "UPDATE #__sermon_$table SET hits='0' WHERE id='$id'";
  $database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$this->setRedirect( "index.php?option=$option&task=stats" );
} // end of resetcount

function checkDB () {
	global $option;
  $database =& JFactory::getDBO();
  $update_needed = 0;
  // check if podcast row exist;
  $query = "SHOW COLUMNS FROM #__sermon_sermons LIKE 'podcast';";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { $update_needed = 1;}
	
	// check if addfile row exist;
  $query = "SHOW COLUMNS FROM #__sermon_sermons like 'addfile'";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { $update_needed = 1;}
	
	// check if speaker2 row exist;
  $query = "SHOW COLUMNS FROM #__sermon_series like 'speaker2'";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { $update_needed = 1;}
	
	// check if speaker4 row exist;
  $query = "SHOW COLUMNS FROM #__sermon_series like 'speaker4'";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { $update_needed = 1;}
	
	return $update_needed;
} // end of checkDB

function updateDB() {
  global $option;
  $database =& JFactory::getDBO();
  echo "<h3>Update database</h3>";
  
  $query = "SHOW COLUMNS FROM #__sermon_sermons LIKE 'podcast';";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { 
    echo "<p>Updating table for selective pocast<p>";
    $query = "ALTER TABLE `#__sermon_sermons` ADD `podcast` TINYINT( 1 ) NOT NULL DEFAULT '1';";
    $database->setQuery( $query );
    $database->query();
  }
	
  $query = "SHOW COLUMNS FROM #__sermon_sermons like 'addfile'";
  $database->setQuery( $query );
  $database->query();
  if ($database->getNumRows() == 0) { 
    echo "<p>Updating table for additional download<p>";
    $query = "ALTER TABLE `#__sermon_sermons` ADD `addfile` TEXT NOT NULL, ADD `addfileDesc` TEXT NOT NULL;";
    $database->setQuery( $query );
    $database->query();
  }
  
  $query = "SHOW COLUMNS FROM #__sermon_series like 'speaker2'";
  $database->setQuery( $query );
  $database->query();
	if ($database->getNumRows() == 0) { 
    echo "<p>Updating table for additional download<p>";
    $query = "ALTER TABLE `#__sermon_series` ADD `speaker2` INT NOT NULL, ADD `speaker3` INT NOT NULL ;";
    $database->setQuery( $query );
    $database->query();
  }
  
  $query = "SHOW COLUMNS FROM #__sermon_series like 'speaker4'";
  $database->setQuery( $query );
  $database->query();
  if ($database->getNumRows() == 0) { 
    echo "<p>Updating table for additional download<p>";
    $query = "ALTER TABLE `#__sermon_series` ADD `speaker4` INT NOT NULL, ADD `speaker5` INT NOT NULL, ADD `speaker6` INT NOT NULL, ADD `speaker7` INT NOT NULL, ADD `speaker8` INT NOT NULL, ADD `speaker9` INT NOT NULL, ADD `speaker10` INT NOT NULL, ADD `speaker11` INT NOT NULL, ADD `speaker12` INT NOT NULL, ADD `speaker13` INT NOT NULL, ADD `speaker14` INT NOT NULL, ADD `speaker15` INT NOT NULL, ADD `speaker16` INT NOT NULL, ADD `speaker17` INT NOT NULL, ADD `speaker18` INT NOT NULL, ADD `speaker19` INT NOT NULL, ADD `speaker20` INT NOT NULL;"; 
    $database->setQuery( $query );
    $database->query();
  }
    
  echo "<p>Finished! Click <a href=\"index.php?option=com_sermonspeaker&task=config\">here to continue!</a></p>"; 
} // end of updateDB

function un_pc() {
  $database =& JFactory::getDBO();
  $id = JRequest::getVar('id', 0);
  $query = "UPDATE `#__sermon_sermons` SET `podcast` = '0' WHERE `id` = ".$id.";";
  $database->setQuery( $query );
  $database->query();
  $this->setRedirect('index.php?option=com_sermonspeaker&task=sermons');
} // end of un_pc

function pc() {
  $database =& JFactory::getDBO();
  $id = JRequest::getVar('id', 0);
  $query = "UPDATE `#__sermon_sermons` SET `podcast` = '1' WHERE `id` = ".$id.";";
  $database->setQuery( $query );
  $database->query();
  $this->setRedirect('index.php?option=com_sermonspeaker&task=sermons');
} // end of pc

function filename_safe($filename) {
  $temp = substr($filename, 0, -4);
  $exttemp = substr($filename,-4);
  // Lower case
  $temp = strtolower($temp);
  // Replace spaces with a '_'
  //$temp = str_replace(" ", "_", $temp);
  // Replace bad characters
  $repl = array(" ","$");
  $temp = str_replace($repl, "_", $temp);
  // Loop through string
  $result = '';
  for ($i=0; $i<strlen($temp); $i++) {
    if (preg_match('([0-9]|[a-z]|_)', $temp[$i])) {
      $result = $result . $temp[$i];
    } 
  }
  // Return filename
  return $result.$exttemp; 
} // end of filename_safe

}

?>
