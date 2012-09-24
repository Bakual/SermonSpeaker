<?php
defined('_JEXEC') or die;

class SermonspeakerController extends JControllerLegacy 
{
	protected $default_view = 'main';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/sermonspeaker.php';

		parent::display();

		return $this;
	}
}