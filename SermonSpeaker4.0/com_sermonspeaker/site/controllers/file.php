<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * File Sermonspeaker Controller
 * Copied and adapted from File Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.6
 */
class SermonspeakerControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get the user
		$user		= JFactory::getUser();

		// Get some data from the request
		$files		= JRequest::getVar('Filedata', '', 'files', 'array');
		$path	= (JRequest::getBool('addfile', false)) ? $params->get('path_addfile') : $params->get('path');
		$append	= ($params->get('append_path', 0)) ? DS.date('Y').DS.date('m') : '';
		$folder	= JPATH_ROOT.DS.$path.$append;

		$return		= JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		if ($return) {
			$this->setRedirect(base64_decode($return));
		}
		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker')) {
			JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			return false;
		}

		$success = false;
		$warning = array();
		$message = array();
		$redirect = '';
		for($i = 0; $i != 2; $i++){
			// Make the filename safe
			$files['name'][$i] = JFile::makeSafe($files['name'][$i]);
			$files['name'][$i] = str_replace(' ', '_', $files['name'][$i]); // Replace spaces in filename as long as makeSafe doesn't do this.

			// Check if filename has more chars than only underscores, making a new filename based on current date/time if not.
			if (count_chars(JFile::stripExt($files['name'][$i]), 3) == '_') {
				$files['name'][$i] = JFactory::getDate()->format("Y-m-d-H-i-s").'.'.JFile::getExt($files['name'][$i]);
			}

			if ($files['name'][$i]){
				// The request is valid
				$err = null;
				$filepath = JPath::clean($folder.DS.strtolower($files['name'][$i]));

				$files['filepath'][$i] = $filepath;

				if (JFile::exists($filepath)) {
					// File exists
					$warning[] = JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS');
					continue;
				}
				if (!JFile::upload($files['tmp_name'][$i], $files['filepath'][$i])) {
					// Error in upload
					$warning[] = JText::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE');
					continue;
				} else {
					$message[] = JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $files['filepath'][$i]);
					$redirect .= 'file'.$i.'=/'.str_replace('\\', '/', substr($files['filepath'][$i], strlen(JPATH_ROOT.DS)));
					$success = true;
				}
			}
		}
		if($warning){
			JError::raiseWarning(100, implode('<br>', $warning));
		}
		if($message){
			$this->setMessage(implode('<br>', $message));
		}
		if ($success){
			$return_url = base64_decode($return);
			if(strpos($return_url, '?')){
				$this->setRedirect(base64_decode($return).'&'.$redirect);
			} else {
				$this->setRedirect(base64_decode($return).'?'.$redirect);
			}
		}
		return $success;
	}
}
