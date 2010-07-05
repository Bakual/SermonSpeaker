<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * SermonSpeaker Component Controller
 */
class SermonspeakerController extends JController
{
	function display() {
		if (!JRequest::getCmd('view')){	// Setting default view
			JRequest::setVar('view', 'sermons');
		} elseif (JRequest::getCmd('view') == 'feed' && (JRequest::getCmd('format') != 'raw')){ // Changing the podcast format to raw output
			$document =& JFactory::getDocument();
			$document = $document->getInstance('raw');
			$document->setMimeEncoding('application/rss+xml');
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
		if ($id == ''){
			die("<html><body OnLoad=\"javascript: alert('I have no clue what you want to download...');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
		$database =& JFactory::getDBO();
		$query = "SELECT sermon_path FROM #__sermon_sermons WHERE id = ".$id;
		$database->setQuery($query);
		$result = $database->loadResult() or die ("<html><body OnLoad=\"javascript: alert('Encountered an error while accessing the database');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		$result = rtrim($result);

		if (substr($result,0,7) == "http://"){ // cancel if link goes to an external source
			die("<html><body OnLoad=\"javascript: alert('This file points to an external source. I can't access it.');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
		$file = str_replace('\\', '/', JPATH_ROOT.$result); // replace \ with /
		if (substr($result, 0, 1) != '/') { // add a leading slash to the sermonpath if not present.
			$result = '/'.$result;
		}
		$filename = explode("/", $file);
		$filename = array_reverse($filename);

		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		if (file_exists($file)) {
			if(ini_get('memory_limit')){
				ini_set('memory_limit','-1'); // if present overriding the memory_limit for php so big mp3 files can be downloaded.
			}
			header("Pragma: public");
			header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/mp3");
			header('Content-Disposition: attachment; filename="'.$filename[0].'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".@filesize($file));
			set_time_limit(0);
			@readfile($file) OR die('Unable to read file!');
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