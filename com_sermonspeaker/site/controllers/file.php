<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\Filesystem\Path;

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerFile extends BaseController
{
	/**
	 * Upload a file
	 *
	 * @return void
	 * @throws \Exception
	 *
	 * @since ?
	 */
	public function upload()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

		/** @var JApplicationSite $app */
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$jinput = $app->input;

		// Get the user
		$user = Factory::getUser();

		// Access check
		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker'))
		{
			throw new Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
		}

		// Create append
		$append = ($params->get('append_path_user', 0)) ? '/' . Factory::getApplication()->getIdentity()->id : '';
		$append .= ($params->get('append_path', 0)) ? '/' . $jinput->get('year', date('Y'), 'int') . '/'
			. str_pad($jinput->get('month', date('m'), 'int'), 2, '0', STR_PAD_LEFT) : '';

		if ($params->get('append_path_lang', 0))
		{
			$lang = $jinput->get('language');

			if (strlen($lang) != 5)
			{
				$lang = Factory::getLanguage()->getTag();
			}

			$append .= '/' . $lang;
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		ClientHelper::setCredentialsFromRequest('ftp');

		// Get files
		$files    = $jinput->files->get('Filedata', '', 'array');
		$redirect = '';

		foreach ($files as $key => $file)
		{
			if ($file['error'])
			{
				continue;
			}

			// Get file extension
			$ext = File::getExt($file['name']);

			// Make filename URL safe. Eg replaces ä with ae.
			$file['name'] = OutputFilter::stringURLSafe(File::stripExt($file['name'])) . '.' . $ext;

			// Check if filename has more chars than only dashes, making a new filename based on current date/time if not.
			if (count_chars(File::stripExt($file['name']), 3) == '-')
			{
				$file['name'] = Factory::getDate()->format("Y-m-d-H-i-s") . '.' . $ext;
			}

			$type = $key ? 'video' : 'audio';

			// Check file extension
			$types = strtolower($params->get($type . '_filetypes'));
			$types = array_map('trim', explode(',', $types));

			if (!in_array(strtolower($ext), $types))
			{
				$app->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_FILETYPE_NOT_ALLOWED', $ext), 'error');
				continue;
			}

			// Fall back to the old 'path' parameter for B/C versions < 5.0.3, always will use audio path.
			$path   = trim($params->get('path_' . $type, $params->get('path', 'images')), '/');
			$folder = JPATH_ROOT . '/' . $path . $append;

			if ($file['name'])
			{
				// The request is valid
				$filepath = Path::clean($folder . '/' . strtolower($file['name']));

				if (file_exists($filepath))
				{
					// File exists
					$app->enqueueMessage(Text::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'), 'warning');
					continue;
				}

				if (!File::upload($file['tmp_name'], $filepath))
				{
					// Error in upload
					$app->enqueueMessage(Text::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE'), 'warning');
					continue;
				}
				else
				{
					$app->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $filepath));
					$redirect .= 'file' . $key . '=/' . str_replace('\\', '/', substr($filepath, strlen(JPATH_ROOT . '/')));
				}
			}
		}

		$return = base64_decode($jinput->post->get('return-url', '', 'base64'));

		// Make sure to only redirect to internal links
		if (!Uri::isInternal($return))
		{
			$return = Uri::base();
		}

		if (!empty($redirect))
		{
			if (strpos($return, '?'))
			{
				$return .= '&' . $redirect;
			}
			else
			{
				$return .= '?' . $redirect;
			}
		}

		if ($return)
		{
			$this->setRedirect($return);
		}
	}
}
