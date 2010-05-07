<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * SermonSpeaker Component Controller
 */
class SermonspeakerController extends JController
{
	function display()
	{
		// Setting default view 
		if ( ! JRequest::getCmd( 'view' ) ) {
			$params	=& JComponentHelper::getParams('com_sermonspeaker');
			switch ($params->get('startpage')) {
				case "1" :
					JRequest::setVar('view', 'speakers' );
					break;
				case "2" :
					JRequest::setVar('view', 'series' );
					break;
				case "3" :
					JRequest::setVar('view', 'sermons' );
					break;
				case "4" :
					JRequest::setVar('view', 'seriessermon' );
					break;
			}
					
		}
		parent::display();
	}
	
	function updateStat ($type, $id) {
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'tables');
		$table =& JTable::getInstance($type, 'Table');
		$table->hit($id);
	}

	function download () {
		$id = JRequest::getInt('id');
		$database =& JFactory::getDBO();
		$query="SELECT sermon_path FROM #__sermon_sermons WHERE id=".$id.";";
		$database->setQuery( $query );
		$result = rtrim($database->loadResult());

		if (substr($row[0]->sermon_path,0,7) == "http://"){
			exit;
		}
		$file = str_replace('\\','/',JPATH_ROOT.$result);
		$filename = explode("/", $file ); 
		$filename = array_reverse($filename); 

		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		if ( file_exists($file) ) {
			header("Pragma: public");
			header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/mp3");
			header('Content-Disposition: attachment; filename="'.$filename[0].'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".@filesize($file));
			set_time_limit(0);
			@readfile($file) OR die("<html><body OnLoad=\"javascript: alert('Unable to read file!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
			exit;
		} else {
			die("<html><body OnLoad=\"javascript: alert('File not found!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
	} // end of download

	function fu_logout () {
		$session = &JFactory::getSession();
		$session->set('loggedin','');
		header('HTTP/1.1 303 See Other');
		header('Location: index.php?option=com_user&task=logout&return=aW5kZXgucGhw'); //redirects to index.php after logging the user out
		return;
	} // end of fu_logoff
}