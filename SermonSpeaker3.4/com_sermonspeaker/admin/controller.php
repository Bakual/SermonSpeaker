<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class SermonspeakerController extends JController { 
/*	function __construct() {
		 registerDefaultTask('display');
	} */
		
	function display() {
		// Setzt einen Standard view 
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'main' );
		}
		parent::display();
	}
	
	function migrate() {
		$msg = NULL;
		$configarr = NULL;
		$castconfigarr = NULL;
		$files = array();
		//Check if the config files are present and copy the settings to the Joomla Parameter if they are
		if (file_exists(JPATH_COMPONENT.DS.'config.sermonspeaker.php')){ 
			require_once(JPATH_COMPONENT.DS.'config.sermonspeaker.php');
			$config	= new sermonConfig;
			$configarr = get_object_vars($config);
			$files[] = JPATH_COMPONENT.DS.'config.sermonspeaker.php';
		}
		if (file_exists(JPATH_COMPONENT.DS.'sermoncastconfig.sermonspeaker.php')){ 
			require_once(JPATH_COMPONENT.DS.'sermoncastconfig.sermonspeaker.php' );
			$castconfig = new sermonCastConfig;
			$castconfigarr = get_object_vars($castconfig);
			$files[] = JPATH_COMPONENT.DS.'sermoncastconfig.sermonspeaker.php';
		}
		if (file_exists(JPATH_COMPONENT.DS.'sermoncastconfig.sermonspeaker.php.dist')){ 
			$files[] = JPATH_COMPONENT.DS.'sermoncastconfig.sermonspeaker.php.dist';
		}
		if (file_exists(JPATH_COMPONENT.DS.'config.sermonspeaker.php.dist')){ 
			$files[] = JPATH_COMPONENT.DS.'config.sermonspeaker.php.dist';
		}
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$paramarr = $params->ToArray();
		$intersect1 = array_intersect_key($configarr, $paramarr); // Vergleich der beiden Arraykeys, wenn gleicher Key vorhanden wird Key => Wert aus erstem Array genommen.
		$intersect2 = array_intersect_key($castconfigarr, $paramarr);
		$newparams = array_merge($intersect1, $intersect2);
		foreach ($newparams as $key => $value){ // Update des Params Objekt mit den neuen Werten
			$params->set($key,$value);
		}
		$paramstr = $params->toString(); // Ausgabe der Werte als String damit sie in die DB passen
		$db =& JFactory::getDBO();
		$query = "UPDATE #__components \n"
				."SET params = \"".$paramstr."\" \n"
				."WHERE `option` = 'com_sermonspeaker' \n"
				."AND parent = 0 \n";
		$db->setQuery($query);
		$result = $db->query();
		if (!$result){
			$msg .= 'Error while saving the parameters: '.$db->getErrorMsg().'<br>';
		} else {
			foreach ($files as $file){
				$check = unlink($file);
				if (!$check){
					$msg .= 'Error while deleting '.$file.'<br>';
				}
			}
			$msg .= JText::_('COM_SERMONSPEAKER_MIGRATED');
		}
		$link = 'index.php?option=com_sermonspeaker&view=main';
		$this->setRedirect($link, $msg);
	}
	
	// This will change the database entries in the fields sermon_path and addfile from $old_path to the path specified in the settings. Use on own risk (/administrator/index.php?option=com_sermonspeaker&task=convert will start it), a backup is recommended
	function convert(){ 
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$new_path = '/'.trim($params->get('path'), '/').'/';
		$old_path = "images";
		$db =& JFactory::getDBO();
		$query = "SELECT id, sermon_path, addfile FROM #__sermon_sermons";
		$db->setQuery($query);
		$sermons = $db->loadAssocList();
		foreach ($sermons as $sermon){
			$sermon_path = str_replace($old_path, $new_path, $sermon);
			$query = "UPDATE #__sermon_sermons SET sermon_path = '".$sermon_path['sermon_path']."', addfile = '".$sermon_path['addfile']."' WHERE id = '".$sermon_path['id']."'";
			$db->setQuery($query);
			$db->query();
		}
		
		return;
	}
}