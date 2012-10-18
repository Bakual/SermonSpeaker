<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class SermonspeakerControllerStatistics extends SermonspeakerController
{
	/**
	 * Custom Constructor (registers additional tasks to methods)
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
	}

	function display( )
	{
		parent::display();
	}

	function resetcount()
	{
		$database	= JFactory::getDBO();
		$jinput		= JFactory::getApplication()->input;
		$id 		= $jinput->get('id', 0, 'int');
		$table 		= $jinput->get('table', '', 'word');

		$query	= "UPDATE #__sermon_".$table." \n"
				. "SET hits='0' \n"
				. "WHERE id='".$id."'"
				;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

	$this->setRedirect("index.php?option=com_sermonspeaker&view=statistics");
	}
}