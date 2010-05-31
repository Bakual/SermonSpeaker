<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeaker extends JView
{
	function display( $tpl = null )
	{
		global $option;

		$edit = JRequest::getBool('edit', true);
		if ($edit) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			$id = $cid[0];
		} else {
			$id = 0;
		}

		$row = &JTable::getInstance('speakers', 'Table');
		$row->load($id);
		
		// build list of categories
		$lists['catid']		= JHTML::_('list.category',  'catid', 'com_sermonspeaker', intval( $row->catid ) );

		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}