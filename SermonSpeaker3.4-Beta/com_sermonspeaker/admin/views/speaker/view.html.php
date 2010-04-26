<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeaker extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;

		$edit = JRequest::getBool('edit', true);
		if ($edit) {
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			$id = $cid[0];
		} else {
			$id = 0;
		}

		$row = &JTable::getInstance('speakers', 'Table');
		$row->load($id);
		
		$this->assignRef('row', $row);

		parent::display($tpl);
	}
}