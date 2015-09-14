<?php
defined('_JEXEC') or die;

class SermonspeakerController extends JControllerLegacy 
{
	protected $default_view = 'main';

	public function display($cachable = false, $urlparams = false)
	{
		$params = JComponentHelper::getParams('com_sermonspeaker');

		if ($params->get('css_icomoon') == '')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NOTSAVED'), 'warning');
		}

		if ($params->get('alt_player'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_PLAYER_DEPRECATED'), 'notice');
		}

		return parent::display();
	}
}