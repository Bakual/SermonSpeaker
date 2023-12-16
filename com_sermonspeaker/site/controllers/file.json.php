<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
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
	 * @return  void  Echoes an AJAX response
	 *
	 * @since ?
	 */
	public function upload()
	{
		// Check for request forgeries
		if (!Session::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JINVALID_TOKEN'),
			);
			echo json_encode($response);

			return;
		}

		// Authorize User
		$user = Factory::getUser();

		if (!$user->authorise('core.create', 'com_sermonspeaker'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JGLOBAL_AUTH_ACCESS_DENIED'),
			);
			echo json_encode($response);

			return;
		}

		// Initialise variables.
		/** @var JApplicationSite $app */
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$jinput = $app->input;

		// Get some data from the request
		$file = $jinput->files->get('file');
		$type = $jinput->get('type', 'audio', 'word');
		$type = (in_array($type, array('audio', 'video', 'addfile'))) ? $type : 'audio';

		if (!$file['name'])
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('COM_SERMONSPEAKER_FU_FAILED'),
			);
			echo json_encode($response);

			return;
		}

		// Get file extension
		$ext = File::getExt($file['name']);

		// Optionally sanitising filenames
		if ($params->get('sanitise_filename', 1))
		{
			// Make filename URL safe. Eg replaces ä with ae.
			$file['name'] = OutputFilter::stringURLSafe(File::stripExt($file['name'])) . '.' . $ext;

			// Make the filename safe
			$file['name'] = File::makeSafe($file['name']);

			// Replace spaces in filename as long as makeSafe doesn't do this.
			$file['name'] = str_replace(' ', '_', $file['name']);

			// Check if filename has more chars than only dashes, making a new filename based on current date/time if not
			if (count_chars(File::stripExt($file['name']), 3) == '-')
			{
				$file['name'] = Factory::getDate()->format("Y-m-d-H-i-s") . '.' . $ext;
			}
		}

		$mode = 0;

		if ($type == 'audio')
		{
			$mode = $params->get('path_mode_audio', 0);
		}
		elseif ($type == 'video')
		{
			$mode = $params->get('path_mode_video', 0);
		}

		// Check for file extension
		$types = strtolower($params->get($type . '_filetypes'));
		$types = array_map('trim', explode(',', $types));

		if (!in_array(strtolower($ext), $types))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::sprintf('COM_SERMONSPEAKER_FILETYPE_NOT_ALLOWED', $ext),
			);
			echo json_encode($response);

			return;
		}

		if ($mode == 2)
		{
			// Add missing constant in PHP < 5.5
			defined('CURL_SSLVERSION_TLSv1') or define('CURL_SSLVERSION_TLSv1', 1);

			// Amazon S3 Upload

			// AWS access info
			$awsAccessKey = $params->get('s3_access_key');
			$awsSecretKey = $params->get('s3_secret_key');
			$region       = $params->get('s3_region');
			$bucket       = $params->get('s3_bucket');
			$folder       = $params->get('s3_folder') ? trim($params->get('s3_folder'), ' /') . '/' : '';

			// Instantiate the class
			$credentials = new Credentials($awsAccessKey, $awsSecretKey);
			$s3          = new S3Client([
				'version'     => 'latest',
				'region'      => $region,
				'credentials' => $credentials,
			]);

			$date   = $jinput->get('date', '', 'string');
			$time   = ($date) ? strtotime($date) : time();
			$folder .= ($params->get('append_path_user', 0)) ? Factory::getApplication()->getIdentity()->id. '/' : '';
			$folder .= ($params->get('append_path', 0)) ? date('Y', $time) . '/' . date('m', $time) . '/' : '';

			if ($params->get('append_path_lang', 0))
			{
				$lang = $jinput->get('language');

				if (!$lang || $lang == '*')
				{
					$jlang = Factory::getLanguage();
					$lang  = $jlang->getTag();
				}

				$folder .= $lang . '/';
			}

			$uri = $folder . $file['name'];

			// Check if file exists
			if ($s3->doesObjectExist($bucket, $uri))
			{
				$response = array(
					'status' => '0',
					'error'  => Text::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'),
				);
				echo json_encode($response);

				return;
			}

			$prefix = ($region === 'us-east-1') ? 's3' : 's3-' . $region;
			$domain = $prefix . '.amazonaws.com/' . $bucket;

			// Upload the file
			try
			{
				$result = $s3->putObject([
					'Bucket'     => $bucket,
					'Key'        => $uri,
					'SourceFile' => $file['tmp_name'],
					'ACL'        => 'public-read',
				]);

				$response = array(
					'status'   => '1',
					'filename' => $file['name'],
					'path'     => $result['ObjectURL'],
					'error'    => Text::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $result['ObjectURL']),
				);
				echo json_encode($response);

				return;
			}
			catch (Exception $e)
			{
				$response = array(
					'status' => '0',
					'error'  => Text::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE'),
				);
				echo json_encode($response);

				return;
			}
		}
		else
		{
			// Regular Upload
			// Fall back to the old 'path' parameter for B/C versions < 5.0.3
			$path   = $params->get('path_' . $type, $params->get('path', 'images'));
			$path   = trim($path, '/');
			$date   = $jinput->get('date', '', 'string');
			$time   = ($date) ? strtotime($date) : time();
			$append = ($params->get('append_path_user', 0)) ? '/' . Factory::getApplication()->getIdentity()->id : '';
			$append .= ($params->get('append_path', 0)) ? '/' . date('Y', $time) . '/' . date('m', $time) : '';

			if ($params->get('append_path_lang', 0))
			{
				$lang = $jinput->get('language');

				if (!$lang || $lang == '*')
				{
					$jlang = Factory::getLanguage();
					$lang  = $jlang->getTag();
				}

				$append .= '/' . $lang;
			}

			$folder = JPATH_ROOT . '/' . $path . $append;

			// Set FTP credentials, if given
			jimport('joomla.client.helper');
			ClientHelper::setCredentialsFromRequest('ftp');

			$filename = $file['name'];

			if ($params->get('sanitise_filename', 1))
			{
				$filename = strtolower($filename);
			}

			$filepath         = Path::clean($folder . '/' . $filename);
			$file['filepath'] = $filepath;

			if (file_exists($filepath))
			{
				// File exists
				$response = array(
					'status' => '0',
					'error'  => Text::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS'),
				);
				echo json_encode($response);

				return;
			}

			if (!File::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				$response = array(
					'status' => '0',
					'error'  => Text::_('COM_SERMONSPEAKER_FU_ERROR_UNABLE_TO_UPLOAD_FILE'),
				);
				echo json_encode($response);

				return;
			}
			else
			{
				$response = array(
					'status'   => '1',
					'filename' => $filename,
					'path'     => str_replace('\\', '/', '/' . $path . $append . '/' . $filename),
					'error'    => Text::sprintf('COM_SERMONSPEAKER_FU_FILENAME', substr($file['filepath'], strlen(JPATH_ROOT))),
				);
				echo json_encode($response);

				return;
			}
		}
	}

	/**
	 * Lookup the ID3 tags
	 *
	 * @return  void  Echoes an AJAX response
	 *
	 * @since ?
	 */
	public function lookup()
	{
		$file = Factory::getApplication()->input->get('file', '', 'string');

		if (!$file)
		{
			$response = array(
				'status' => '0',
				'msg'    => Text::_('COM_SERMONSPEAKER_ERROR_ID3'),
			);
			echo json_encode($response);

			return;
		}

		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/id3.php';
		$params = ComponentHelper::getParams('com_sermonspeaker');
		$id3    = SermonspeakerHelperId3::getID3($file, $params);

		// Format the date to the language specific format
		if ($id3['sermon_date'])
		{
			$id3['sermon_date'] = HTMLHelper::date($id3['sermon_date'], Text::_('DATE_FORMAT_FILTER_DATETIME'));
		}

		if ($id3)
		{
			$response           = $id3;
			$response['status'] = 1;
		}
		else
		{
			$response = array(
				'status' => '0',
				'msg'    => Text::_('COM_SERMONSPEAKER_ERROR_ID3'),
			);
		}

		echo json_encode($response);
	}
}
