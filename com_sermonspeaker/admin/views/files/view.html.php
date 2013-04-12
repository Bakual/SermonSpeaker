<?php
defined('_JEXEC') or die;
class SermonspeakerViewFiles extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->state	= $this->get('state');
		$this->items = $this->get('items');

		parent::display($tpl);
	}
}