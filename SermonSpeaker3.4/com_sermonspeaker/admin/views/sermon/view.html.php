<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSermon extends JView
{
	function display( $tpl = null )
	{
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

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		$edit = JRequest::getBool('edit', true);
		if ($edit) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			$id = $cid[0];
		} else {
			$id = 0;
		}

		$row = &JTable::getInstance('sermons', 'Table');
		$row->load($id);

		if ($id3_file = JRequest::getString('file')){
			// Reading ID3 Tags
			$row->sermon_path = $id3_file;
			require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'id3'.DS.'getid3'.DS.'getid3.php');
			$getID3 	= new getID3;
			$path		= JPATH_SITE.str_replace('/',DS,$id3_file);
			$FileInfo	= $getID3->analyze($path);
			getid3_lib::CopyTagsToComments($FileInfo);
			$id3 = array();
			$id3['sermon_time']		= $FileInfo['playtime_string'];
			$id3['sermon_title']	= $FileInfo['comments_html']['title'][0];
			if ($FileInfo['comments_html']['track_number'][0] != ""){
				$id3['sermon_number']	= $FileInfo['comments_html']['track_number'][0]; // ID3v2 Tag
			} else {
				$id3['sermon_number']	= $FileInfo['comments_html']['track'][0]; // ID3v1 Tag
			}

			$db =& JFactory::getDBO();
			$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['album'][0]."';";
			$db->setQuery($query);
			$id3['series_id'] 	= $db->loadRow();

			$query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['artist'][0]."';";
			$db->setQuery($query);
			$id3['speaker_id']	= $db->loadRow();

			$id3['notes'] 	= NULL;
			$id3['sermon_scripture'] = NULL;
			if ($params->get('fu_id3_comments') == 'ref'){
				if ($FileInfo['comments_html']['comments'][0] != ""){
					$id3['sermon_scripture'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
				} else {
					$id3['sermon_scripture'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
				}
			} else {
				if ($FileInfo['comments_html']['comments'][0] != ""){
					$id3['notes'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
				} else {
					$id3['notes'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
				}
			}
			foreach ($id3 as $key => $value){
				if ($value){
					$row->$key = $value;
				}
			}
		}

		// getting the files with extension $filters from $path and its subdirectories for sermons
		$path = JPATH_ROOT.DS.$params->get('path');
		$filters = array('.mp3','.m4a','.flv','.mp4','.wmv');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path, $filter, true, true),$filesabs);
		}
		
		// changing the filepaths relativ to the joomla root
		$root = JPATH_ROOT;
		$lsdir = strlen($root);
		$sermons = array();
		foreach($filesabs as $file) {
			$sermons[]->file = str_replace('\\','/',substr($file,$lsdir));
		}
		
		// getting the files with extension $filters from $path and its subdirectories for addfiles
		$path_addfile = JPATH_ROOT.DS.$params->get('path_addfile');
		$filters = array('.pdf','.bmp','.png','.jpg','.gif','.txt','.doc');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path_addfile, $filter, true, true),$filesabs);
		}
		// changing the filepaths relativ to the joomla root
		$addfiles = array();
		foreach($filesabs as $file) {
			$addfiles[]->file = str_replace('\\','/',substr($file,$lsdir));
		}

		$lists = array();
		// build list of categories
		$lists['catid']		= JHTML::_('list.category',  'catid', 'com_sermonspeaker', intval( $row->catid ) );

		$speakers = &$this->get('Speakers');
		$series = &$this->get('Series');
		$lists['sermon_path_choice'] = JHTML::_('select.genericlist', $sermons, 'sermon_path_choice', 'disabled="disabled"', 'file', 'file', $row->sermon_path);
		$lists['addfile_choice'] = JHTML::_('select.genericlist', $addfiles, 'addfile_choice', 'disabled="disabled"', 'file', 'file', $row->addfile);
		$lists['created_by'] = JHTML::_('list.users', 'created_by', $row->created_by, 0, '', 'name', 0);
		$lists['speaker_id'] = JHTML::_('select.genericlist', $speakers, 'speaker_id', '','id', 'name', $row->speaker_id);
		$lists['series_id'] = JHTML::_('select.genericlist', $series, 'series_id', '', 'id', 'series_title', $row->series_id);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published);
		$lists['podcast'] = JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"', $row->podcast);
		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
	}
}