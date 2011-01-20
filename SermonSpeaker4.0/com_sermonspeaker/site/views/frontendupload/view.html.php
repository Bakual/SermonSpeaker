<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFrontendupload extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		$params		= $app->getParams();
//		$params	= $this->state->get('params'); // TODO: Maybe work with state for params.

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'get', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'get', JREQUEST_ALLOWRAW);

		// Perform the log in.
		if ($credentials['username'] && $credentials['password']){
			$app->login($credentials);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker')){
			JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			return;
		} else {
			$file = JRequest::getVar('upload', null, 'files', 'array');
 			if (!$file) {
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
			} else { 			
				// Form was submited, move the file!
				jimport('joomla.filesystem.file');
//				jimport('joomla.client.helper');  // TODO: needed?
//				JClientHelper::setCredentialsFromRequest('ftp'); // TODO: needed?
				$filename = JFile::makeSafe($file['name']);
				$filename = str_replace(' ', '_', $filename); // replace spaces with underscore in filename
				$dest = JPATH_ROOT.DS.$params->get('path').DS.$params->get('fu_destdir').DS.$filename;
				if (JFile::exists($dest)) { 
					// file exists already
					JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'));
					parent::display($tpl);
					return;
				}
				$allowed = array('mp3', 'm4a', 'flv', 'mp4', 'm4v', 'wmv' );
				if (!in_array(strtolower(JFile::getExt($filename)), $allowed)) {
					// file extension not supported
					JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_FU_ERROR_EXT'));
					parent::display($tpl);
					return;
				} else {
					if (!JFile::upload($file['tmp_name'], $dest)) {
						JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_FU_FAILED'));
						parent::display($tpl);
						return;
					} else {
						$app->redirect('index.php?option=com_sermonspeaker&view=fu_details&filename='.$filename);
					}
				}
			}
		}
	}	
}