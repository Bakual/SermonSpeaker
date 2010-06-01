<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSermon extends JView
{
	function display( $tpl = null )
	{
		// add Javascript for Form Elements enable and disable
		$js = 'var currentEnabled = null;
        function enableElement(elem) {
          if (currentEnabled) {
            currentEnabled.disabled = true;
          }
          elem.disabled = false;
          currentEnabled = elem;
          
        }
        function disElement(elem) {
          elem.disabled = true;
        }';
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($js);

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