<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeaker extends JView
{
	function display( $tpl = null )
	{
		$row = &JTable::getInstance('speakers', 'Table');
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

		// build list of categories
		$lists['catid']		= JHTML::_('list.category',  'catid', 'com_sermonspeaker', intval( $row->catid ) );

		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}