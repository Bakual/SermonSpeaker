<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSerie extends JView
{
	function display( $tpl = null )
	{
		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		$row = &JTable::getInstance('series', 'Table');
		$edit = JRequest::getBool('edit', true);
		if ($edit) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			$id = $cid[0];
			$row->load($id);
			if (!$row->created_by){
				$user =& JFactory::getUser();
				$row->created_by = $user->id;
			}
			if (!$row->created_on){
				$row->created_on = date('Y-m-d H:i:s');
			}
		} else {
			$id = 0;
			$row->load($id);
			$user =& JFactory::getUser();
			$row->created_by = $user->id;
			$row->created_on = date('Y-m-d H:i:s');
		}

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
		$avatars[0]->name = JText::_('COM_SERMONSPEAKER_SELECT_NOAVATAR');
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

		$lists['avatar'] = JHTML::_('select.genericlist', $avatars,'avatar','','file','name',$row->avatar);  

		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}