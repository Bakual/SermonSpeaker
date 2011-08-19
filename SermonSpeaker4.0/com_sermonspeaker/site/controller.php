<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * SermonSpeaker Component Controller
 */
class SermonspeakerController extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getWord('view', 'sermons');
		JRequest::setVar('view', $vName);

		if (JRequest::getCmd('view') == 'feed' && (JRequest::getCmd('format') != 'raw')){
			$this->setRedirect('index.php?option=com_sermonspeaker&view=feed&format=raw');
			return;
		}

		$safeurlparams = array('id'=>'INT','limit'=>'INT','limitstart'=>'INT','filter_order'=>'CMD','filter_order_Dir'=>'CMD','lang'=>'CMD','year'=>'INT','month'=>'INT');

		return parent::display($cachable,$safeurlparams);
	}

	function download () {
		$id = JRequest::getInt('id');
		if ($id == ''){
			die("<html><body OnLoad=\"javascript: alert('I have no clue what you want to download...');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
		$db =& JFactory::getDBO();
		if (JRequest::getWord('type') == 'video'){
			$query = "SELECT videofile FROM #__sermon_sermons WHERE id = ".$id;
		} else {
			$query = "SELECT audiofile FROM #__sermon_sermons WHERE id = ".$id;
		}
		$db->setQuery($query);
		$result = $db->loadResult() or die ("<html><body OnLoad=\"javascript: alert('Encountered an error while accessing the database');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		$result = rtrim($result);

		if (substr($result, 0, 7) == 'http://'){ // redirect if link goes to an external source
			$result = str_replace('http://player.vimeo.com/video/', 'http://vimeo.com/', $result);
			$this->setRedirect($result);
			return;
		}
		$result = str_replace('\\', '/', $result); // replace \ with /
		if (substr($result, 0, 1) != '/') { // add a leading slash to the sermonpath if not present.
			$result = '/'.$result;
		}
		$file = JPATH_ROOT.$result;
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
			$fSize = @filesize($file);
			$chunksize = 3 * (1024 * 1024); // how many bytes per chunk
			if ($fSize > $chunksize) {
				$handle = fopen($file, 'rb');
				$buffer = '';
				while (!feof($handle)) {
					$buffer = fread($handle, $chunksize);
					echo $buffer;
					ob_flush();
					flush();
				}
				fclose($handle);
			} else {
				@readfile($file) OR die('Unable to read file!');
			}
			exit;
		} else {
			die("<html><body OnLoad=\"javascript: alert('File not found!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
	} // end of download
}