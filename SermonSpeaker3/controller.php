<?php
defined('_JEXEC') or die('Restricted access');

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
		$speaker_names = $db->loadObjectList();
    
    $db =& JFactory::getDBO(); 
		$query = "SELECT avatar_name,id FROM #__sermon_avatars";
		$db->setQuery( $query ); 
		$avatar_names = $db->loadObjectList();
    
    $db =& JFactory::getDBO();
    $query = "SELECT * FROM #__users ORDER BY name";
    $db->setQuery( $query ); 
		$users = $db->loadObjectList();
		
    $lists['speaker_id'] = JHTML::_('select.genericlist', $speaker_names,'speaker_id','','id','name',$row->speaker_id);
    $lists['avatar_id'] = JHTML::_('select.genericlist', $avatar_names,'avatar_id','','id','avatar_name',$row->avatar_id);  
    $lists['created_by'] = JHTML::_('select.genericlist', $users,'created_by','','id','name',$row->created_by);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published); 

		HTML_SermonSpeaker::editSeries($row, $lists, $option); 
	} //end of edit
 
	function save() { 
		global $option; 

		$row =& JTable::getInstance('series', 'Table');	
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		switch ($this->_task)        
		{
			case 'apply': 
				$msg = 'Changes to Sermon Series saved'; 
				$link = 'index.php?option=' . $option . '&task=edit&cid[]='. $row->id; 
				break;
			
			case 'save': 
			default: 
				$msg = 'Sermon Series Saved'; 
				$link = 'index.php?option=' . $option; 
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
		
		$query = "SELECT * FROM #__sermon_series";
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
		
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
		$limitstart = JRequest::getVar('limitstart', 0); 
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT count(*) FROM #__sermon_sermons";
		$db->setQuery( $query ); 
		$total = $db->loadResult(); 
		
		$query = "SELECT * FROM #__sermon_sermons";
		$db->setQuery( $query, $limitstart, $limit ); 
		$rows = $db->loadObjectList(); 
		
		if ($db->getErrorNum()) { 
			echo $db->stderr(); 
			return false; 
		} 
		
		jimport('joomla.html.pagination'); 	
		$pageNav = new JPagination($total, $limitstart, $limit); 
		HTML_SermonSpeaker::showSermons( $option, $rows, $pageNav );
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
		
		HTML_SermonSpeaker::editSermons($row, $lists, $option);
	} //end of editSermons

  function saveSermons() { 
		global $option; 
		$row =& JTable::getInstance('sermons', 'Table');
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		
		$row->notes = JRequest::getVar('notes','','post','string',JREQUEST_ALLOWRAW);
	
		if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
	
		switch ($this->_task)        
		{
			case 'apply': 
				$msg = 'Changes to Sermon applied'; 
				$link = 'index.php?option='.$option.'&task=editSermons&cid[]='. $row->id; 
				break;
			
			case 'save': 
			default: 
				$msg = 'Changes to Sermon saved'; 
				$link = 'index.php?option='.$option.'&task=sermons'; 
				break; 
		}	 
		$this->setRedirect($link, $msg); 
	} //end of saveSermons

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
		
    //Diplay Settings
    $lists['sermonresults'] = JHTML::_('select.genericlist', $results,'sermonresults','','value','text',$config->sermonresults);
    $lists['limit_speaker'] = JHTML::_('select.booleanlist', 'limit_speaker', 'class="inputbox"', $config->limit_speaker);
    $lists['speaker_intro'] = JHTML::_('select.booleanlist', 'speaker_intro', 'class="inputbox"', $config->speaker_intro);
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
    
		HTML_SermonSpeaker::showConfig($row, $lists, $option); 
	} //end of edit
	
	
	function saveConfig() {
     global $option;
     
	   $configfile = "components/com_sermonspeaker/config.sermonspeaker.php";
	   $permission = is_writable($configfile);
	   if (!$permission) {
		   $this->setRedirect('index.php?option='.$option.'&task=config',"Configuration file not writeable!");
		   return;
	   }
	   $sermonresults = JRequest::getVar('sermonresults','post',string);
	   $limit_speaker = JRequest::getVar('limit_speaker','post',string);
	   $track_speaker = JRequest::getVar('track_speaker','post',string);
     $track_series = JRequest::getVar('track_series','post',string);
	   $track_sermon = JRequest::getVar('track_sermon','post',string);
	   $speaker_intro = JRequest::getVar('speaker_intro','post',string);
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
	   $config = "<?php \n";
	   $config .= "/*\n";
	   $config .= "* File: config.sermonspeaker.php\n";
	   $config .= "*  @ speaker - A SermonSpeaker Component\n";
	   $config .= "*  @ Copyright 2006 - 2008 by Steve Shiflett & Martin Hess\n";
	   $config .= "*  @ Website - http://joomlacode.org/gf/project/sermon_speaker/\n";
	   $config .= "*/\n";
	   $config .= "\n";
	   $config .= "defined( '_JEXEC' ) or die( 'Restricted access' );\n";
	   $config .= "\n";
	   $config .= "class sermonConfig {\n";
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
		   $this->setRedirect('index.php?option='.$option.'&task=config',"SermonCast configuration file not writeable!");
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
	   $config = "<?php \n";
	   $config .= "/*\n";
	   $config .= "* File: sermoncastconfig.sermonspeaker.php\n";
	   $config .= "*  @ speaker - A SermonSpeaker Component\n";
	   $config .= "*  @ Copyright 2006 - 2008 by Steve Shiflett & Martin Hess\n";
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
	   $config .= "} \n?";
	   $config .= ">";
     if ($fp = fopen("$cf", "w")) {
		   fputs($fp, $config, strlen($config));
		   fclose ($fp);
	   }
	   $this->setRedirect('index.php?option='.$option.'&task=config', "Configuration settings saved.");
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
			$this->setRedirect( "index2.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Only files of type gif, png, jpg, or bmp can be uploaded to the avatar directory.  It only makes sense if you think about it." );
		}
	return;
  } // end of insert_avatar_info
  
  function do_upload($file, $dest_dir) {
    global $clearUploads;
  
  	if (file_exists($dest_dir.strtolower($file['name']))) {
  		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Upload FAILED. File allready exists" );
  	}
  	if ((strcasecmp(substr($file['name'],-4),".gif")) && (strcasecmp(substr($file['name'],-4),".jpg")) && (strcasecmp(substr($file['name'],-4),".png")) && (strcasecmp(substr($file['name'],-4),".bmp")) &&(strcasecmp(substr($file['name'],-4),".mp3"))) {
  		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Only files of type gif, png, jpg, bmp or mp3 can be uploaded" );
  	}
  	if (!move_uploaded_file($file['tmp_name'], $dest_dir.strtolower($file['name']))){
  		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Upload FAILED" );
  	} else {
  	  chmod($dest_dir.$file['name'], 0666);
  		// echo "ok: ".substr($file['name'],-4)."and stratcasecmp = ".strnatcasecmp(substr($file['name'],-4),".mp3");
  		// Now make them fill out the new sermon form
  		// redirect to the following url if the file is an mp3
  		if( strnatcasecmp(substr($file['name'],-4),".mp3") == 0 ){
  			$loc = "./components/com_sermonspeaker/media/";
  			$this->setRedirect("index.php?option=com_sermonspeaker&task=newsermonupload&uploadedfile=".$loc.$file['name']);
  		}
  		// otherwise....
  		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Upload complete" );
  	}
    $clearUploads = true;
  } // end of do_upload
  
  function deleteFile() {
    $listdir = JRequest::getVar('listdir','');
    $delfile = JRequest::getVar('delFile','');
    $listdir = JRequest::getVar('listdir','');
  	$del_image = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.DS.$delfile;
    unlink($del_image);
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
    $folder_name = JRequest::getVar('foldername','');
    $dirPath = JRequest::getVar('dirPath','');
    $listdir = JRequest::getVar('listdir','');
    if(strlen($folder_name) >0) {
  	if (eregi("[^0-9a-zA-Z_/]", $folder_name)) {
  		$this->setRedirect( "index.php?option=com_sermonspeaker&task=media&listdir=".$_POST['dirPath'], "Directory name must only contain alphanumeric characters and no spaces please." );
  	}
  	$folder = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$dirPath.DS.$folder_name;
  
  	if(!is_dir($folder) && !is_file($folder)){
  		mkdir($folder,0777);
  		chmod($folder,0777);
  		$refresh_dirs = true;
  	}
   }
   $this->setRedirect('index.php?option=com_sermonspeaker&task=media&listdir='.$listdir); 
  } // end of create_folder
  
  function deletefolder() {
    $listdir = JRequest::getVar('listdir','');
    $delFolder = JRequest::getVar('delFolder','');
  	$del_folder = JPATH_SITE.DS."components".DS."com_sermonspeaker".DS."media".$listdir.$delFolder;
  	rmdir($del_folder);
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
}

/*********************************************/
/* Stuff to remove                           */
/*********************************************/	
/*	
	function comments() { 
		global $option, $mainframe;
		
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
		$limitstart = JRequest::getVar('limitstart', 0);
		
		$db =& JFactory::getDBO(); 
		$query = "SELECT count(*) FROM #__reviews_comments"; 
		$db->setQuery( $query ); 
		$total = $db->loadResult(); 
		
		$query = "SELECT c.*, r.name FROM #__reviews_comments AS c LEFT JOIN #__reviews AS r ON r.id = c.review_id "; 
		$db->setQuery( $query, $limitstart, $limit ); 
		$rows = $db->loadObjectList(); 
		
		if ($db->getErrorNum()) 
		{ 
			echo $db->stderr(); 
			return false; 
		}
		
		jimport('joomla.html.pagination'); 
		$pageNav = new JPagination($total, $limitstart, $limit); 
		HTML_SermonSpeaker::showComments( $option, $rows, $pageNav ); 
	} 
	
	function editComment() { 
		global $option; 

		$row =& JTable::getInstance('comment', 'Table'); 
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' ); 
		$id = $cid[0]; 
		$row->load($id); 
		HTML_SermonSpeaker::editComment($row, $option); 
	}
	
	function saveComment() { 
		global $option; 

		$row =& JTable::getInstance('comment', 'Table'); 
		
		if (!$row->bind(JRequest::get('post'))) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		}
		
		if (!$row->store()) { 
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n"; 
			exit(); 
		} 
		
		$this->setRedirect('index.php?option=' . $option . '&task=comments', 'Comment changes saved'); 
	}
	
	function removeComment() { 
		global $option; 
		$cid = JRequest::getVar( 'cid', array(), '', 'array' ); 
		$db =& JFactory::getDBO(); 

		if(count($cid)) 
		{ 
			$cids = implode( ',', $cid ); 
			$query = "DELETE FROM #__reviews_comments WHERE id IN ( $cids )"; 
			$db->setQuery( $query ); 
			
			if (!$db->query()) { 
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n"; 
			} 
		} 
		
		$this->setRedirect( 'index.php?option=' . $option . '&task=comments' ); 
	}
	*/
}

?>
