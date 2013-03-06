<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

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
class SermonspeakerControllerFile extends JControllerLegacy
{
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{
		// Check for request forgeries
		if (!JSession::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN'),
			);
			echo json_encode($response);
			return;
		}

		// Authorize User
		$user		= JFactory::getUser();
		if (!$user->authorise('core.create', 'com_sermonspeaker')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JGLOBAL_AUTH_ACCESS_DENIED')
			);
			echo json_encode($response);
			return;
		}

		// Initialise variables.
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$jinput	= JFactory::getApplication()->input;

		// Get some data from the request
		$file	= JRequest::getVar('Filedata', '', 'files', 'array');
		$type	= $jinput->get('type', 'audio', 'word');

		if (!$file['name']) {
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_SERMONSPEAKER_FU_FAILED')
			);
			echo json_encode($response);
			return;
		}

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);
		$file['name']	= str_replace(' ', '_', $file['name']); // Replace spaces in filename as long as makeSafe doesn't do this.

		// Check if filename has more chars than only underscores, making a new filename based on current date/time if not.
		if (count_chars(JFile::stripExt($file['name']), 3) == '_') {
			$file['name'] = JFactory::getDate()->format("Y-m-d-H-i-s").'.'.JFile::getExt($file['name']);
		}

		$mode = 0;
		if ($type == 'audio'){
			$mode = $params->get('path_mode_audio', 0);
		} elseif ($type == 'video'){
			$mode = $params->get('path_mode_video', 0);
		}

		if ($mode == 2){
			// Amazon S3 Upload
			//include the S3 class   
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/s3/S3.php';
			//AWS access info   
			$awsAccessKey 	= $params->get('s3_access_key');
			$awsSecretKey 	= $params->get('s3_secret_key');
			$bucket			= $params->get('s3_bucket');
			//instantiate the class
			$s3 = new S3($awsAccessKey, $awsSecretKey);
			$region	= $s3->getBucketLocation($bucket);
			$prefix	= ($region == 'US') ? 's3' : 's3-'.$region;

			// Upload the file
			if($s3->putObjectFile($file['tmp_name'], $bucket, JFile::makeSafe($file['name']), S3::ACL_PUBLIC_READ)){
				$response = array(
					'status' => '1',
					'filename' => $file['name'],
					'path' => 'http://'.$prefix.'.amazonaws.com/'.$bucket.'/'.$file['name'],
					'error' => JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $prefix.'.amazonaws.com/'.$bucket.'/'.$file['name'])
				);
				echo json_encode($response);
				return;
			} else {
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
		} else {
			// Regular Upload
			$path	= ($type == 'addfile') ? $params->get('path_addfile') : $params->get('path');
			$path	= trim($path, '/');
			$date	= $jinput->get('date', '', 'string');
			$time	= ($date) ? strtotime($date) : time();
			$append	= ($params->get('append_path', 0)) ? '/'.date('Y', $time).'/'.date('m', $time) : '';
			if($params->get('append_path_lang', 0)){
				$lang	= $jinput->get('select-language');
				if(!$lang || $lang == '*'){
					$jlang	= JFactory::getLanguage();
					$lang	= $jlang->getTag();
				}
				$append	.= '/'.$lang;
			}
			$folder	= JPATH_ROOT.'/'.$path.$append;

			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');

			$err = null;
			$filepath = JPath::clean($folder.'/'.strtolower($file['name']));

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
		}
	}

	function lookup(){
		$file	= JFactory::getApplication()->input->get('file', '', 'string');

		if($file){
			require_once JPATH_COMPONENT_SITE.'/helpers/id3.php';
			$params	= JComponentHelper::getParams('com_sermonspeaker');
			$id3 = SermonspeakerHelperId3::getID3($file, $params);

			if ($id3){
				$response = $id3;
				$response['status']	= 1;
			} else {
				$response = array(
					'status' => '0',
					'msg' => JText::_('COM_SERMONSPEAKER_ERROR_ID3')
				);
			}
		} else {
				$response = array(
					'status' => '0',
					'msg' => JText::_('COM_SERMONSPEAKER_ERROR_ID3')
				);
		}

		echo json_encode($response);
	}
}
