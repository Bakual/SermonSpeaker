<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSerie extends JView
{
	function display( $tpl = null )
	{
		global $option;

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		$edit = JRequest::getBool('edit', true);
		if ($edit) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			$id = $cid[0];
		} else {
			$id = 0;
		}

		$row = &JTable::getInstance('series', 'Table');
		$row->load($id);
		
		$speakers = &$this->get('Speakers');


		// getting the files with extension $filters from $path and its subdirectories for avatars
		$path = JPATH_ROOT.DS.$params->get('path_avatar');
		$path2 = JPATH_ROOT.DS.'components'.DS.'com_sermonspeaker'.DS.'media'.DS.'avatars';
		$filters = array('.jpg','.gif','.png','.bmp');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path, $filter, true, true),$filesabs);
		}
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path2, $filter, true, true),$filesabs);
		}
		
		// changing the filepaths relativ to the joomla root
		$root = JPATH_ROOT;
		$lsdir = strlen($root);
		$avatars = array();
		$avatars[0]->name = JText::_('NOAVATAR');
		$avatars[0]->file = '';
		$i = 1;
		foreach($filesabs as $file){
			$avatars[$i]->name = trim(strrchr($file,DS),DS);
			$avatars[$i]->file = str_replace('\\','/',substr($file,$lsdir));
			$i++;
		}
		$lists = array();
		$lists['created_by'] = JHTML::_('list.users', 'created_by', $row->created_by, 0, '', 'name', 0);

		// build list of categories
		$lists['catid']		= JHTML::_('list.category',  'catid', 'com_sermonspeaker', intval( $row->catid ) );

		$addspeaker = $speakers;
		$value = array('name'=>JText::_('NOSPEAKER'),'id'=>'');
		array_unshift($addspeaker,$value); // insert an empty option for Speaker 2-20

		$lists['speaker_id'] = JHTML::_('select.genericlist', $speakers,'speaker_id','','id','name',$row->speaker_id);
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
		$lists['avatar'] = JHTML::_('select.genericlist', $avatars,'avatar','','file','name',$row->avatar);  

		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}