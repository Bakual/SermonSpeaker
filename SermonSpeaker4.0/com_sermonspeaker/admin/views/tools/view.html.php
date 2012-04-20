<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewTools extends JView
{
	function display( $tpl = null )
	{

		// Check if PreachIt is installed
		$app = JFactory::getApplication();
		$db		= JFactory::getDbo();
		$query	= "SHOW TABLES";
		$db->setQuery($query);
		$tables	= $db->loadResultArray();
		$prefix	= $app->getCfg('dbprefix');
		$this->pi	= (in_array($prefix.'pistudies', $tables)) ? true : false;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'), 'tools');

		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}