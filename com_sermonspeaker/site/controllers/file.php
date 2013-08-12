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
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$jinput		= $app->input;

		// Get the user
		$user		= JFactory::getUser();

		// Access check
		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker'))
		{
			JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			return false;
		}

		// Create append
		$append	= ($params->get('append_path', 0)) ? '/'.$jinput->get('year', date('Y'), 'int').'/'.str_pad($jinput->get('month', date('m'), 'int'), 2 ,'0', STR_PAD_LEFT) : '';
		if ($params->get('append_path_lang', 0))
		{
			$lang = $jinput->get('language');
			if (strlen($lang) != 5)
			{
				$lang	= JFactory::getLanguage()->getTag();
			}
			$append .= '/'.$lang;
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Get files
		$files	= $jinput->files->get('Filedata', '', 'array');

		foreach ($files as $key => $file)
		{
			if ($file['error'])
			{
				continue;
			}

			$file['name'] = JFile::makeSafe($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']); // Replace spaces in filename as long as makeSafe doesn't do this.

			// Check if filename has more chars than only underscores, making a new filename based on current date/time if not.
			if (count_chars(JFile::stripExt($file['name']), 3) == '_')
			{
				$file['name'] = JFactory::getDate()->format("Y-m-d-H-i-s").'.'.JFile::getExt($file['name']);
			}

			$type = $key ? 'video' : 'audio';

			// Check file extension
			$ext	= JFile::getExt($file['name']);
			$types	= $params->get($type.'_filetypes');
			$types	= array_map('trim', explode(',', $types));
			if (!in_array($ext, $types))
			{
				$app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_FILETYPE_NOT_ALLOWED', $ext), 'error');
				continue;
			}

			// Fall back to the old 'path' parameter for B/C versions < 5.0.3, always will use audio path.
			$path	= trim($params->get('path_'.$type, $params->get('path', 'images')), '/');
			$folder	= JPATH_ROOT.'/'.$path.$append;

			if ($file['name'])
			{
				// The request is valid
				$filepath = JPath::clean($folder.'/'.strtolower($file['name']));

				if (JFile::exists($filepath))
				{
					// File exists
					$app->enqueueMessage(JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'), 'warning');
					continue;
				}
				if (!JFile::upload($file['tmp_name'], $filepath))
				{
					// Error in upload
					$app->enqueueMessage(JText::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE'), 'warning');
					continue;
				}
				else
				{
					$app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $filepath));
					$redirect .= 'file'.$i.'=/'.str_replace('\\', '/', substr($filepath, strlen(JPATH_ROOT.'/')));
				}
			}
		}

		$return		= base64_decode($jinput->post->get('return-url', '', 'base64'));
		if (!empty($redirect))
		{
			if(strpos($return, '?'))
			{
				$return .= '&'.$redirect;
			}
			else
			{
				$return .= '?'.$redirect;
			}
		}
		if ($return)
		{
			$this->setRedirect($return);
		}
		return $success;
	}
}