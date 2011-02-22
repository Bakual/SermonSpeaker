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
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		if ($fu_destdir = $params->get('fu_destdir')) {
			$fu_destdir .= '/';
		}
		$folder		= JPATH_ROOT.DS.$params->get('path').DS.$fu_destdir;
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Set the redirect
		if ($return) {
			$this->setRedirect(base64_decode($return));
		}

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name'])){
			// The request is valid
			$err = null;
			$filepath = JPath::clean($folder.strtolower($file['name']));

			$object_file = new JObject($file);
			$object_file->filepath = $filepath;

			if (JFile::exists($filepath)) {
				// File exists
				JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'));
				return false;
			} elseif (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker')) {
				// File does not exist and user is not authorised to create
				JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
				return false;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			} else {
				$this->setMessage(JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', substr($file['filepath'], strlen(JPATH_ROOT))));
				$this->setRedirect(base64_decode($return).'&file=/'.$params->get('path').'/'.$fu_destdir.strtolower($file['name']));
				return true;
			}
		} else {
			$this->setRedirect('index.php', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return false;
		}
	}
}
