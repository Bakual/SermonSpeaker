<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewHelp extends JView
{
	function display( $tpl = null )
	{
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;

		if (file_exists(JPATH_COMPONENT.DS.'views'.DS.'help'.DS.'tmpl'.DS.$language.'.help.php')) {
			$help = JPATH_COMPONENT.DS.'views'.DS.'help'.DS.'tmpl'.DS.$language.'.help.php';
		} else {
			$help = JPATH_COMPONENT.DS.'views'.DS.'help'.DS.'tmpl'.DS.'en-GB.help.php';
		}
		
		$this->assignRef('help', $help);
	
		parent::display($tpl);
	}
}