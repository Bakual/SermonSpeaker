<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonupload\Site\Helper;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Media\Administrator\Exception\FileExistsException;
use Joomla\Component\Media\Administrator\Model\ApiModel;
use Joomla\Component\Media\Administrator\Model\MediaModel;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die();

/**
 * Helper class for SermonUpload module
 *
 * @since  1.0
 */
class SermonuploadHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	/**
	 * Get Losung from XML
	 *
	 * @return  array
	 *
	 * @throws \Exception
	 * @since   1.0
	 */
	public static function fileUploadAjax(): array
	{
		// Check for request forgeries
		if (!Session::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JINVALID_TOKEN'),
			);

			return $response;
		}

		// Authorize User
		$user = Factory::getApplication()->getIdentity();

		if (!$user->authorise('core.create', 'com_sermonspeaker'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JGLOBAL_AUTH_ACCESS_DENIED'),
			);

			return $response;
		}

		// Initialise variables.
		$app    = Factory::getApplication();
		$cparams = $app->getParams('com_sermonspeaker');
		$jinput = $app->input;

		// Load language
		$language = Factory::getApplication()->getLanguage();
		$language->load('mod_sermonupload', JPATH_BASE . '/modules/mod_sermonupload');

		// Get some data from the request
		$file = $jinput->files->get('file');
		$type = $jinput->get('type', 'audio', 'word');
		$type = (in_array($type, array('audio', 'video', 'addfile'))) ? $type : 'audio';

		if (!$file['name'])
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('MOD_SERMONUPLOAD_UPLOAD_FAILED'),
			);

			return $response;
		}

		// Get file extension
		$ext = File::getExt($file['name']);

		// Optionally sanitising filenames
		if ($cparams->get('sanitise_filename', 1))
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

		// Check for file extension
		$types = strtolower($cparams->get($type . '_filetypes'));
		$types = array_map('trim', explode(',', $types));

		if (!in_array(strtolower($ext), $types))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::sprintf('MOD_SERMONUPLOAD_FILETYPE_NOT_ALLOWED', $ext),
			);

			return $response;
		}

		$path   = $cparams->get('path_' . $type, 'local-images:/');
		$date   = $jinput->get('date', '', 'string');
		$time   = ($date) ? strtotime($date) : time();
		$append = ($cparams->get('append_path_user', 0)) ? '/' . Factory::getApplication()->getIdentity()->id : '';
		$append .= ($cparams->get('append_path', 0)) ? '/' . date('Y', $time) . '/' . date('m', $time) : '';

		if ($cparams->get('append_path_lang', 0))
		{
			$lang = $jinput->get('language');

			if (!$lang || $lang == '*')
			{
				$jlang = Factory::getApplication()->getLanguage();
				$lang  = $jlang->getTag();
			}

			$append .= '/' . $lang;
		}

		$folder = JPATH_ROOT . '/' . $path . $append;

		$filename = $file['name'];

		if ($cparams->get('sanitise_filename', 1))
		{
			$filename = strtolower($filename);
		}

		$filepath         = Path::clean($folder . '/' . $filename);
		$file['filepath'] = $filepath;

		// TODO: Check with mediamanager
		if (file_exists($filepath))
		{
			// File exists
			$response = array(
				'status' => '0',
				'error'  => Text::_('MOD_SERMONUPLOAD_UPLOAD_ERROR_EXISTS'),
			);

			return $response;
		}

		$pathinfo = explode(':', $path, 2);

		$mediaManager = new ApiModel();

		try
		{
			$mediaManager->createFile($pathinfo[0], $filename, $pathinfo[1] . $append, $file['tmp_name'],false);

			// "Translating" Adapter names for displaying upload path
			$provider_adapter   = explode('-', $pathinfo[0]);
			$model     = new MediaModel;
			$providers = $model->getProviders();

			foreach ($providers as $provider)
			{
				if ($provider_adapter[0] === $provider->name)
				{
					$adapterDisplayName = $provider->displayName;

					break;
				}
			}

			$path = $adapterDisplayName . ': ' . $provider_adapter[1] . $pathinfo[1] . $append . '/' . $filename;
			$path = str_replace('\\', '/', $path);
			$path = str_replace('//', '/', $path);

			$response = array(
				'status'   => '1',
				'filename' => $filename,
				'error'    => Text::sprintf('MOD_SERMONUPLOAD_UPLOAD_FILENAME', $path),
			);
		}
		catch (FileExistsException $e)
		{
			// Error in upload
			$response = array(
				'status' => '0',
				'error'  => Text::_('MOD_SERMONUPLOAD_UPLOAD_ERROR_EXISTS'),
			);
		}
		catch (Exception $e)
		{
			// Error in upload
			$response = array(
				'status' => '0',
				'error'  => Text::_('MOD_SERMONUPLOAD_UPLOAD_ERROR_UNABLE_TO_UPLOAD_FILE'),
			);
		}

		return $response;
	}

	/**
	 * Loads JavaScript for the uploader into Document Header
	 *
	 * @param string $identifier Unique identifier
	 * @param string $type       Filetype (audio, video, addfile)
	 * @param   /Joomla/Registry/Registry  $type        SermonSpeaker params
	 *
	 * @return  void
	 *
	 * @since   ?
	 */
	public function loadUploaderScript(string $identifier, string $type, $params): void
	{
		$identifier = $identifier . $type;
		$uploadURL  = Uri::base() . 'index.php?option=com_ajax&module=sermonupload&method=fileUpload&'
			. Session::getFormToken() . '=1&format=json';

		switch ($type)
		{
			case 'audio':
				$uploadURL .= '&mediatypes=1';
				break;
			case 'video':
				$uploadURL .= '&mediatypes=2';
				break;
			case 'addfile':
				$uploadURL .= '&mediatypes=0,3';
				break;
		}

		$plupload_script = '
			jQuery(document).ready(function() {
				var uploader_' . $identifier . ' = new plupload.Uploader({
					browse_button: "browse_' . $identifier . '",
					url: "' . $uploadURL . '&type=' . $type . '",
					drop_element: "' . $identifier . '_drop",
		';

		// Add File filters
		$types = $params->get($type . '_filetypes');
		$types = array_map('trim', explode(',', $types));
		$types = implode(',', $types);
		$text  = strtoupper('MOD_SERMONUPLOAD_FIELD_TYPES_OPTION_' . $identifier);

		if ($types)
		{
			$plupload_script .= '
					filters : {
						mime_types: [
							{title : "' . Text::_($text, 'true') . '", extensions : "' . $types . '"},
						]
					},';
		}

		$plupload_script .= '
				});

				uploader_' . $identifier . '.init();

				uploader_' . $identifier . '.bind("FilesAdded", function(up, files) {
					var html = "";
					plupload.each(files, function(file) {
						html += "<div id=\"" + file.id + "\" class=\"alert alert-info\">"
						 	+ file.name + " (" + plupload.formatSize(file.size) + ") "
							+ "<progress id=\"" + file.id + "_progress\" max=\"100\"></progress></div>";
					});
					document.getElementById("filelist_' . $identifier . '").innerHTML += html;
					uploader_' . $identifier . '.start();
				});

				uploader_' . $identifier . '.bind("UploadProgress", function(up, file) {
					document.getElementById(file.id + "_progress").setAttribute("value", file.percent);
					document.getElementById(file.id + "_progress").innerHtml = "<b>" + file.percent + "%</b>";
				});

				uploader_' . $identifier . '.bind("FileUploaded", function(up, file, response) {
					if(response.status == 200){
						var data = JSON.parse(response.response).data;
						if (data.status == 1){
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-success");
							document.getElementById(file.id).innerHTML = data.error;
						}else{
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-danger");
							jQuery("#" + file.id + "_progress").replaceWith("<br>&raquo; ' . Text::_('ERROR') . ': " + data.error);
						}
					}
				});

				uploader_' . $identifier . '.bind("Error", function(up, err) {
					document.getElementById("filelist_' . $identifier . '").innerHTML += "<div class=\"alert alert-danger\">Error #"
						+ err.code + ": " + err.message + "</div>";
				});

				uploader_' . $identifier . '.bind("PostInit", function(up) {
					jQuery("#upload-noflash").remove();
					if(up.features.dragdrop){
						jQuery("#' . $identifier . '_drop").addClass("drop-area");
					}
				});
			});
		';
		Factory::getDocument()->addScriptDeclaration($plupload_script);
	}

	/**
	 * Function to determine max upload value
	 *
	 * @return  string  Lower PHP Setting Value
	 *
	 * @since ?
	 */
	static public function getMaxUploadValue(): string
	{
		// Check some PHP settings for upload limit so I can show it as an info
		$post_max_size       = ini_get('post_max_size');
		$upload_max_filesize = ini_get('upload_max_filesize');

		return (self::return_bytes($post_max_size) < self::return_bytes($upload_max_filesize)) ? $post_max_size : $upload_max_filesize;
	}


	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual
	 *
	 * @param string $val Value from the PHP setting
	 *
	 * @return  int  $val  Value in bytes
	 *
	 * @since ?
	 */
	static private function return_bytes(string $val): int
	{
		$val  = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		$val  = (int) $val;

		switch ($last)
		{
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}
}
