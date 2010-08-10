<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFu_step_1 extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$session 	= &JFactory::getSession();

		// Securitycheck
		if ($session->get('loggedin','') != 'loggedin') {
			header('HTTP/1.1 303 See Other');
			header('Location: index.php?option=com_sermonspeaker');
			exit;
		}
		
		$file = JRequest::getVar('upload', null, 'files', 'array');
  
		if ($file) {
			// Form was submited
			jimport('joomla.filesystem.file');
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');
			$filename = JFile::makeSafe($file['name']);
			$filename = str_replace(' ', '_', $filename); // replace spaces with underscore in filename
			$dest = JPATH_ROOT.DS.$params->get('path').DS.$params->get('fu_destdir').DS.$filename;
			if (file_exists($dest)) { // file exists already

				$this->setLayout('errorexist');
				parent::display($tpl);
				
				return;
			}
			$allowed = array('mp3','wmv','flv');
			if (in_array(strtolower(JFile::getExt($filename)), $allowed)) {
				if ( JFile::upload($file['tmp_name'], $dest) ) {
					header('Location: index.php?option=com_sermonspeaker&view=fu_step_2&filename='.$filename);
					exit;
				} else {
					//Redirect and throw an error message
					echo "<br>Error";
				}
			} else { // file extension not supported
				$this->setLayout('errorextension');
				parent::display($tpl);
			}

		} else { // First call...
			// add Javascript to prevent Submit button clicked more than once
			$submitOnce = 'var submitted = 0;
				function submitOnce(form) {
					if (submitted) {
						alert("Form already submitted, please be patient");
						return false;}
					if (!submitted) {
						form.submitit.disabled=true;
						submitted = 1;
						form.submit();}
				} ';
			$document =& JFactory::getDocument();
			$document->addScriptDeclaration($submitOnce);

			parent::display($tpl);
		}
	}	
}