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

		$params		=& JComponentHelper::getParams('com_sermonspeaker');
		$session 	=& JFactory::getSession();

		// Securitycheck
		if ($session->get('loggedin','') != 'loggedin') {
			header('HTTP/1.1 303 See Other');
			header('Location: index.php?option=com_sermonspeaker');
			exit;
		}
		
		$db =& JFactory::getDBO();

		if (JRequest::getBool('submitted', false, 'POST')) {
			// Form was submitted

			$sql['speaker_id'] 			= JRequest::getInt('speaker_id', '', 'POST');
			$sql['series_id'] 			= JRequest::getInt('speaker_id', '', 'POST');
			$file 						= JRequest::getString('filename', '', 'POST');
			$sql['sermon_path']			= '/'.$params->get('path').'/'.$params->get('fu_destdir').'/'.$file;
			$sql['sermon_title']		= JRequest::getString('sermon_title', '', 'POST');
			$sql['alias']				= JRequest::getCmd('alias', JFilterOutput::stringURLSafe($sql['sermon_title']), 'POST');
			$sql['sermon_number']		= JRequest::getInt('sermon_number', '', 'POST');
			$sql['sermon_scripture']	= JRequest::getString('sermon_scripture', '', 'POST');
			$sql['sermon_date']			= JRequest::getString('sermon_date', '', 'POST');
			// making sure that the time is valid formatted
			$tarr = explode(':',JRequest::getString('sermon_time', '', 'POST'));
			foreach ($tarr as $tar){
				$tar = (int)$tar;
				$tar = str_pad($tar, 2, '0', STR_PAD_LEFT);
			}
			if (count($tarr) == 2) {
				$sql['sermon_time'] = '00:'.$tarr[0].':'.$tarr[1];
			} elseif (count($tarr) == 3) {
				$sql['sermon_time'] = $tarr[0].':'.$tarr[1].':'.$tarr[2];
			}
			$sql['notes']		= JRequest::getVar('notes', '', '', 'STRING', JREQUEST_ALLOWHTML);
			$sql['published']	= JRequest::getInt('published', '0', 'POST');
			$sql['podcast']		= JRequest::getInt('podcast', '0', 'POST');
			$user =& JFactory::getUser();
			$sql['created_by']	= $user->id;
			$sql['created_on']	= date('Y-m-d');

			$keys	= implode('`,`', array_keys($sql));
			$values = implode("','", $sql);
			$query	= "INSERT INTO #__sermon_sermons \n"
					. "(`".$keys."`) \n"
					. "VALUES ('".$values."')";
			$db->setQuery($query);
			if (!$db->query()) { die("SQL error".$db->stderr(true)); }
			
			// Change Template to "Saved" (Step 3)
			$this->setLayout('saved');
		} else {
			// Form wasn't submitted!
			
			// add Javascript for Form Elements enable and disable
			$enElem = 'function enableElement(ena_elem, dis_elem) {
				ena_elem.disabled = false;
				dis_elem.disabled = true;
			}';
			// add Javascript for Scripture Links buttons
			$sendText = 'function sendText(elem, open, close) {
				elem.value = open+elem.value+close;
			}';
			
			$document =& JFactory::getDocument();
			$document->addScriptDeclaration($enElem);
			$document->addScriptDeclaration($sendText);

			JHTML::_('behavior.calendar'); 
			JHTML::_('behavior.modal', 'a.modal-button');
			
			$editor	=& JFactory::getEditor();
			$file 	= JRequest::getString('filename', '');
			$path 	= JPATH_SITE.DS.$params->get('path').DS.$params->get('fu_destdir').DS.$file;
			
			// Reading ID3 Tags
			require_once(JPATH_COMPONENT.DS.'id3'.DS.'getid3'.DS.'getid3.php');
			$getID3 	= new getID3;
			$FileInfo	= $getID3->analyze($path);
			getid3_lib::CopyTagsToComments($FileInfo);
			$id3 = array();
			$id3['time']	= $FileInfo['playtime_string'];
			$id3['title']	= $FileInfo['comments_html']['title'][0];
			$id3['alias']	= JFilterOutput::stringURLSafe($FileInfo['comments_html']['title'][0]);
			if ($FileInfo['comments_html']['track_number'][0] != ""){
				$id3['number']	= $FileInfo['comments_html']['track_number'][0]; // ID3v2 Tag
			} else {
				$id3['number']	= $FileInfo['comments_html']['track'][0]; // ID3v1 Tag
			}

			$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['album'][0]."';";
			$db->setQuery($query);
			$id3['series'] 	= $db->loadRow();

			$query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['artist'][0]."';";
			$db->setQuery($query);
			$id3['speaker']	= $db->loadRow();

			$id3['notes'] 	= NULL;
			$id3['ref']		= NULL;
			if ($params->get('fu_id3_comments') == 'ref'){
				if ($FileInfo['comments_html']['comments'][0] != ""){
					$id3['ref'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
				} else {
					$id3['ref'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
				}
			} else {
				if ($FileInfo['comments_html']['comments'][0] != ""){
					$id3['notes'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
				} else {
					$id3['notes'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
				}
			}
			// Preparing Dropdown Lists
			// todo: probably move Databasestuff to Model
			$query = "SELECT name, id FROM #__sermon_speakers";
			$db->setQuery($query);
			$speaker_names = $db->loadObjectList();

			$query = "SELECT series_title, id FROM #__sermon_series";
			$db->setQuery($query);
			$series_title = $db->loadObjectList();
			
			$lists['speaker_id']	= JHTML::_('select.genericlist', $speaker_names, 'speaker_id', '', 'id', 'name', $id3['speaker']);
			$lists['series_id']		= JHTML::_('select.genericlist', $series_title, 'series_id', '', 'id', 'series_title', $id3['series']);
			$lists['published']		= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', '1');
			$lists['podcast']		= JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"', '1');
			
			// Push the Data to the Template
			$this->assignRef('lists',$lists);
			$this->assignRef('file',$file);
			$this->assignRef('editor',$editor);
			$this->assignRef('id3',$id3);
		}
		parent::display($tpl);
	}
}