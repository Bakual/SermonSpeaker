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
		if (!JRequest::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN'),
			);
			echo json_encode($response);
			return;
		}

		// Initialise variables.
		$params		= JComponentHelper::getParams('com_sermonspeaker');

		// Get the user
		$user		= JFactory::getUser();

		// Get some data from the request
		$file	= JRequest::getVar('Filedata', '', 'files', 'array');
		$path	= (JRequest::getBool('addfile', false)) ? $params->get('path_addfile') : $params->get('path');
		$path	= trim($path, '/');
		$date	= JRequest::getString('date');
		$time	= ($date) ? strtotime($date) : time();
		$append	= ($params->get('append_path', 0)) ? DS.date('Y', $time).DS.date('m', $time) : '';
		if($params->get('append_path_lang', 0)){
			$lang	= JRequest::getCmd('select-language');
			if(!$lang || $lang == '*'){
				$jlang	= JFactory::getLanguage();
				$lang	= $jlang->getTag();
			}
			$append	.= DS.$lang;
		}
		$folder	= JPATH_ROOT.DS.$path.$append;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);
		$file['name']	= str_replace(' ', '_', $file['name']); // Replace spaces in filename as long as makeSafe doesn't do this.

		// Check if filename has more chars than only underscores, making a new filename based on current date/time if not.
		if (count_chars(JFile::stripExt($file['name']), 3) == '_') {
			$file['name'] = JFactory::getDate()->format("Y-m-d-H-i-s").'.'.JFile::getExt($file['name']);
		}

		if ($file['name']) {
			// The request is valid
			$err = null;
			$filepath = JPath::clean($folder.DS.strtolower($file['name']));

			$object_file = new JObject($file);
			$object_file->filepath = $filepath;

			if (JFile::exists($filepath)) {
				// File exists
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS')
				);
				echo json_encode($response);
				return;
			} elseif (!$user->authorise('core.create', 'com_sermonspeaker')) {
				// File does not exist and user is not authorised to create
				$response = array(
					'status' => '0',
					'error' => JText::_('JGLOBAL_AUTH_ACCESS_DENIED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				// Error in upload
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			} else {
				$response = array(
					'status' => '1',
					'filename' => strtolower($file['name']),
					'path' => str_replace('\\', '/', '/'.$path.$append.'/'.strtolower($file['name'])),
					'error' => JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', substr($file['filepath'], strlen(JPATH_ROOT)))
				);
				echo json_encode($response);
				return;
			}
		} else {
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_SERMONSPEAKER_FU_FAILED')
			);

			echo json_encode($response);
			return;
		}
	}
}
