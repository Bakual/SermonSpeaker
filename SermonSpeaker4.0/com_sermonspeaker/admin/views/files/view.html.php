<?php
defined('_JEXEC') or die;
class SermonspeakerViewFiles extends JViewLegacy
{
	function display($tpl = null)
	{
		// Switch Layout if in Joomla 3.0
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($this->getLayout().'30');
		}

		$this->state	= $this->get('state');
		$this->items = $this->get('items');

		parent::display($tpl);
	}
}