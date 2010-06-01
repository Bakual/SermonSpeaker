<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFu_step_2 extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$session 	= &JFactory::getSession();

		// Securitycheck
		if ($session->get('loggedin','') != 'loggedin') {
			header('HTTP/1.1 303 See Other');
			header('Location: index.php?option=com_sermonspeaker');
			exit;
		}
		
		$vars = JRequest::get();
		$notes = JRequest::getVar('notes','','','STRING',JREQUEST_ALLOWHTML);
		if ($vars[published] != "") {
			// Form was sumitted
			// todo: probably move Databasestuff to Model
			$file = JRequest::getVar('filename');
			$path = DS.$params->get('path').$params->get('fu_destdir').DS.$file;
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
			
			// Change Template to "Saved" (Step 3)
			$this->setLayout('saved');
		} else {
			// Form wasn't submitted!
			
			JHTML::_('behavior.calendar'); 
			JHTML::_('behavior.modal', 'a.modal-button');
			
			$editor =& JFactory::getEditor(); 
			$file = JRequest::getVar('filename');
			$path = JPATH_SITE.DS.$params->get('path').DS.$params->get('fu_destdir').DS.$file;
			require_once(JPATH_COMPONENT.DS.'id3'.DS.'getid3'.DS.'getid3.php');
			$getID3 = new getID3;
			$FileInfo = $getID3->analyze($path);
			getid3_lib::CopyTagsToComments($FileInfo);
			// todo: probably move Databasestuff to Model
			$db =& JFactory::getDBO(); 
			$query = "SELECT name,id FROM #__sermon_speakers";
			$db->setQuery( $query ); 
			$speaker_names = $db->loadObjectList();

			$db =& JFactory::getDBO(); 
			$query = "SELECT series_title,id FROM #__sermon_series";
			$db->setQuery( $query ); 
			$series_title = $db->loadObjectList();
			
			$time = $FileInfo['playtime_string'];

			if ($params->get('fu_id3_title') != "-") {
				switch ($params->get('fu_id3_title')) {
					case "Artist"  : $id3title = $FileInfo['comments_html']['artist'][0]; break;
					case "Title"   : $id3title = $FileInfo['tags']['id3v2']['title'][0]; break;
					case "Album"   : $id3title = $FileInfo['comments_html']['album'][0]; break;
					case "Track"   : $id3title = $FileInfo['comments_html']['track'][0]; break;
					case "Comment" : $id3title = $FileInfo['comments_html']['comment'][0]; break;
				}
			} else {
				$id3title="";
			}

			if ($params->get('fu_id3_series') != "-") {
				switch ($params->get('fu_id3_series')) {
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

			if ($params->get('fu_id3_ref') != "-") {
				switch ($params->get('fu_id3_ref')) {
					case "Artist"  : $id3ref = $FileInfo['comments_html']['artist'][0]; break;
					case "Title"   : $id3ref = $FileInfo['tags']['id3v2']['title'][0]; break;
					case "Album"   : $id3ref = $FileInfo['comments_html']['album'][0]; break;
					case "Track"   : $id3ref = $FileInfo['comments_html']['track'][0]; break;
					case "Comment" : $id3ref = $FileInfo['comments_html']['comment'][0]; break;
				}
			} else {
				$id3ref="";
			}

			if ($params->get('fu_id3_number') != "-") {
				switch ($params->get('fu_id3_number')) {
					case "Artist"  : $id3number = $FileInfo['comments_html']['artist'][0]; break;
					case "Title"   : $id3number = $FileInfo['tags']['id3v2']['title'][0]; break;
					case "Album"   : $id3number = $FileInfo['comments_html']['album'][0]; break;
					case "Track"   : $id3number = $FileInfo['comments_html']['track'][0]; break;
					case "Comment" : $id3number = $FileInfo['comments_html']['comment'][0]; break;
				}
			} else {
				$id3number="";
			}

			if ($params->get('fu_id3_notes') != "-") {
				switch ($params->get('fu_id3_notes')) {
					case "Artist"  : $id3notes = $FileInfo['comments_html']['artist'][0]; break;
					case "Title"   : $id3notes = $FileInfo['tags']['id3v2']['title'][0]; break;
					case "Album"   : $id3notes = $FileInfo['comments_html']['album'][0]; break;
					case "Track"   : $id3notes = $FileInfo['comments_html']['track'][0]; break;
					case "Comment" : $id3notes = $FileInfo['comments_html']['comment'][0]; break;
				}
			} else {
				$id3notes="";
			}

			if ($params->get('fu_id3_speaker') != "-") {
				$db =& JFactory::getDBO();
				switch ($params->get('fu_id3_speaker')) {
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

			$lists['speaker_id']	= JHTML::_('select.genericlist', $speaker_names,'speaker_id','','id','name',$id3speaker_id);
			$lists['series_id']		= JHTML::_('select.genericlist', $series_title,'series_id','','id','series_title',$id3series_id);
			$lists['published']		= JHTML::_('select.booleanlist', 'published', 'class="inputbox"');
			$lists['podcast']		= JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"');
			
			// Push the Data to the Template
			$this->assignRef('lists',$lists);
			$this->assignRef('file',$file);
			$this->assignRef('time',$time);
			$this->assignRef('editor',$editor);
			$this->assignRef('id3notes',$id3notes);
			$this->assignRef('id3title',$id3title);
			$this->assignRef('id3ref',$id3ref);
			$this->assignRef('id3number',$id3number);
			$this->assignRef('id3ref',$id3ref);
		}
		parent::display($tpl);
	}	
}