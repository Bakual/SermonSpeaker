<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Field\FilelistField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

/**
 * Creates the filelist dropdown for sermon file select
 *
 * @since ?
 */
class JFormFieldCustomFileList extends FilelistField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	public $type = 'CustomFileList';

	/**
	 * The component params.
	 *
	 * @var    Joomla\Registry\Registry
	 *
	 * @since ?
	 */
	private $params;

	/**
	 * The file path
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	private $file;

	/**
	 * Mode: 0 = Default, 1 = Vimeo, 2 = Amazon S3, 3 = Extern
	 *
	 * @var    integer
	 *
	 * @since ?
	 */
	private $mode;

	/**
	 * Method to get the field input markup for the custom filelist.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   5.1.2
	 */
	protected function getInput()
	{
		$this->params = ComponentHelper::getParams('com_sermonspeaker');

		// Get and sanitize file parameter
		$this->file = (string) $this->element['file'];
		$this->file = (in_array($this->file, array('audio', 'video', 'addfile'))) ? $this->file : 'audio';

		// Mode: 0 = Default, 1 = Vimeo, 2 = Amazon S3, 3 = Extern
		$this->mode = $this->params->get('path_mode_' . $this->file, 0);

		$html = '';

		// Check Filename for possible problems
		if ($this->params->get('sanitise_filename', 1))
		{
			$filename = File::stripExt(basename($this->value));

			// Remove query part (eg for YouTube URLs
			if ($pos = strpos($filename, '?'))
			{
				$filename = substr($filename, 0, $pos);
			}

			if ($filename != ApplicationHelper::stringURLSafe($filename))
			{
				$html .= '<div class="alert alert-warning" role="alert">'
					. Text::_('COM_SERMONSPEAKER_FILENAME_NOT_IDEAL')
					. '</div>';
			}
		}

		$html .= '<div class="input-group">
						<button class="btn btn-secondary"
							type="button" onclick="toggleElement(\'' . $this->fieldname . '\', 0);">
							<span id="' . $this->fieldname . '_text_icon" class="icon-radio-checked text-success"></span>
						</button>
					<input name="' . $this->name . '" id="' . $this->id . '_text" class="form-control ' . $this->class . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT) . '" type="text">';

		// Add Lookup button if not addfile field
		if ($this->file != 'addfile')
		{
			$html .= '<button class="btn btn-secondary lookup-button"
							type="button" data-lookup="' . $this->id . '_text"
							title="' . Text::_('COM_SERMONSPEAKER_LOOKUP') . '">
							<span class="fas fa-magic" data-lookup="' . $this->id . '_text"></span>
						</button>
						<button class="btn btn-secondary"
							type="button"
							onclick="let player = document.getElementById(\'player_' . $this->file . '\');player.src=\'' . Uri::root() . '\'+document.getElementById(\'' . $this->id . '_text\').value;player.classList.remove(\'hidden\');player.play();"
							title="' . Text::_('COM_SERMONSPEAKER_PREVIEW') . '">
							<span class="fas fa-play"></span>
						 </button>';
		}

		// Add Google Picker if enabled and not audio field
		if ($this->params->get('googlepicker') && $this->file != 'audio')
		{
			$html .= '<button class="btn btn-secondary"
							type="button" onclick="create' . ucfirst($this->file) . 'Picker();"
							title="' . Text::_('COM_SERMONSPEAKER_GOOGLEPICKER_TIP') . '">
							<img src="' . Uri::root() . 'media/com_sermonspeaker/icons/16/drive.png">
						</button>';
		}

		$html .= '</div>
				<div class="input-group">
					<button class="btn btn-secondary"
						type="button" onclick="toggleElement(\'' . $this->fieldname . '\', 1);">
						<span id="' . $this->fieldname . '_icon" class="icon-radio-unchecked text-danger"></span>
					</button>';


		// Don't put disabled into the XML. It will break since J3.8.12.
		$this->disabled = true;
		$html           .= parent::getInput();

		if (!$this->mode && $this->file != 'addfile')
		{
			$html .= '<button class="btn btn-secondary lookup-button"
							type="button" data-lookup="' . $this->id . '"
							title="' . Text::_('COM_SERMONSPEAKER_LOOKUP') . '">
							<span class="fas fa-magic" data-lookup="' . $this->id . '"></span>
						</button>';
		}

		$html .= '</div>';

		if ($this->file != 'addfile')
		{
			$html .= '<br><' . $this->file . ' id="player_' . $this->file . '" controls class="hidden" src=""></' . $this->file . '>';
		}

		$html .= $this->getUploader();

		return $html;
	}

	/**
	 * Generates the Uploader
	 *
	 * @return string
	 *
	 * @since ?
	 */
	protected function getUploader()
	{
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'com_sermonspeaker/plupload/plupload.full.min.js', array('relative' => true));

		// Load localisation
		$tag  = str_replace('-', '_', Factory::getLanguage()->getTag());
		$path = '/media/com_sermonspeaker/js/plupload/i18n/';
		$file = $tag . '.js';

		if (file_exists(JPATH_SITE . '/' . $path . $file))
		{
			HTMLHelper::_('script', 'com_sermonspeaker/plupload/i18n/' . $file, array('relative' => true));
		}
		else
		{
			$tag_array = explode('_', $tag);
			$file      = $tag_array[0] . '.js';

			if (file_exists(JPATH_SITE . '/' . $path . $file))
			{
				HTMLHelper::_('script', 'com_sermonspeaker/plupload/i18n/' . $file, array('relative' => true));
			}
		}

		$uploadURL = Uri::base() . 'index.php?option=com_sermonspeaker&task=file.upload&'
			. Session::getFormToken() . '=1&format=json';

		$plupload_script = '
			jQuery(document).ready(function() {
				var uploader_' . $this->fieldname . ' = new plupload.Uploader({
					browse_button: "browse_' . $this->fieldname . '",
					url: "' . $uploadURL . '&type=' . $this->file . '",
					drop_element: "' . $this->fieldname . '_drop",
		';

		// Add File filters
		$types = $this->params->get($this->file . '_filetypes');
		$types = array_map('trim', explode(',', $types));
		$types = implode(',', $types);
		$text  = strtoupper('COM_SERMONSPEAKER_FIELD_' . $this->fieldname . '_LABEL');

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

				uploader_' . $this->fieldname . '.init();
				var closeButton = "<button type=\"button\" class=\"close\" data-bs-dismiss=\"alert\">&times;</button>";

				uploader_' . $this->fieldname . '.bind("FilesAdded", function(up, files) {
					var html = "";
					plupload.each(files, function(file) {
						html += "<div id=\"" + file.id + "\" class=\"alert alert-info\">"
						 	+ file.name + " (" + plupload.formatSize(file.size) + ") "
							+ "<progress id=\"" + file.id + "_progress\" max=\"100\"></progress></div>";
					});
					document.getElementById("filelist_' . $this->fieldname . '").innerHTML += html;
					uploader_' . $this->fieldname . '.start();
				});

				uploader_' . $this->fieldname . '.bind("BeforeUpload", function(up, file) {
					up.setOption("multipart_params", {
						"date":document.getElementById("' . $this->formControl . '_sermon_date").value,
						"language":document.getElementById("' . $this->formControl . '_language").value,
					})
				});

				uploader_' . $this->fieldname . '.bind("UploadProgress", function(up, file) {
					document.getElementById(file.id + "_progress").setAttribute("value", file.percent);
					document.getElementById(file.id + "_progress").innerHtml = "<b>" + file.percent + "%</b>";
				});

				uploader_' . $this->fieldname . '.bind("FileUploaded", function(up, file, response) {
					if(response.status == 200){
						var data = jQuery.parseJSON(response.response);
						if (data.status == 1){
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-success");
							document.getElementById(file.id).innerHTML = data.error + closeButton;
							document.getElementById("' . $this->id . '_text").value = data.path;
						}else{
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-danger");
							jQuery("#" + file.id + "_progress").replaceWith(" &raquo; ' . Text::_('ERROR') . ': " + data.error + closeButton);
						}
					}
				});

				uploader_' . $this->fieldname . '.bind("Error", function(up, err) {
					document.getElementById("filelist_' . $this->fieldname . '").innerHTML += "<div class=\"alert alert-danger\">Error #"
						+ err.code + ": " + err.message + closeButton + "</div>";
				});

				uploader_' . $this->fieldname . '.bind("PostInit", function(up) {
					jQuery("#upload-noflash").remove();
					if(up.features.dragdrop){
						jQuery("#' . $this->fieldname . '_drop").addClass("drop-area");
					}
				});
			});
		';
		Factory::getDocument()->addScriptDeclaration($plupload_script);

		return '<div id="plupload_' . $this->fieldname . '" class="uploader">
					<div id="filelist_' . $this->fieldname . '" class="filelist"></div>
					<button type="button" id="browse_' . $this->fieldname . '" class="btn btn-secondary">'
			. Text::_('COM_SERMONSPEAKER_UPLOAD')
			. '</button>
				</div>';
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since ?
	 */
	protected function getOptions()
	{
		if (!$this->mode)
		{
			$dir = trim($this->params->get('path_' . $this->file, 'images'), '/');

			// Add user ID to the directory if enabled.
			if ($this->params->get('append_path_user', 0))
			{
				// Always populate the list based on active user.
				$append = '/' . Factory::getApplication()->getIdentity()->id;

				// Check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT . '/' . $dir . $append) ? $dir . $append : $dir;
			}

			// Add year/month to the directory if enabled.
			if ($this->params->get('append_path', 0))
			{
				// In case of an edit, we check for the sermon_date and choose the year/month of the sermon.
				$append = ($ts = strtotime($this->form->getValue('sermon_date'))) ? '/' . date('Y', $ts) . '/' . date('m', $ts) : '/' . date('Y') . '/' . date('m');

				// Check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT . '/' . $dir . $append) ? $dir . $append : $dir;
			}

			// Add language to the directory if enabled.
			if ($this->params->get('append_path_lang', 0))
			{
				// In case of an edit, we check for the language set, otherwise we use the active language.
				$language = $this->form->getValue('language');
				$jlang    = Factory::getLanguage();
				$append   = ($language && ($language != '*')) ? '/' . $language : '/' . $jlang->getTag();

				// Check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT . '/' . $dir . $append) ? $dir . $append : $dir;
			}

			$this->directory = $dir;

			// Set file filter from params
			$filetypes = $this->params->get($this->file . '_filetypes');

			if ($filetypes)
			{
				$filetypes        = array_map('trim', explode(',', $filetypes));
				$filter           = '\.' . implode('$|\.', $filetypes) . '$';
				$this->fileFilter = $filter;
			}

			// Get the field options.
			$options = parent::getOptions();

			// Add directory to the value.
			foreach ($options as $option)
			{
				$option->value = '/' . $dir . '/' . $option->value;
			}

			return $options;
		}
		elseif ($this->mode == 1)
		{
			$options = array();
			$url     = 'http://vimeo.com/api/v2/' . $this->params->get('vimeo_id') . '/videos.xml';

			if ($xml = simplexml_load_file($url))
			{
				foreach ($xml->video as $video)
				{
					$option['value'] = $video->url;
					$option['text']  = $video->title;
					$options[]       = $option;
				}

				return $options;
			}
		}
		elseif ($this->mode == 2)
		{
			// Initialize variables.
			$options = array();

			// Add missing constant in PHP < 5.5
			defined('CURL_SSLVERSION_TLSv1') or define('CURL_SSLVERSION_TLSv1', 1);

			// AWS access info
			$awsAccessKey = $this->params->get('s3_access_key');
			$awsSecretKey = $this->params->get('s3_secret_key');
			$region       = $this->params->get('s3_region');
			$bucket       = $this->params->get('s3_bucket');
			$folder       = $this->params->get('s3_folder') ? trim($this->params->get('s3_folder'), ' /') . '/' : '';

			// Instantiate the class
			$credentials = new Credentials($awsAccessKey, $awsSecretKey);
			$s3          = new S3Client([
				'version'     => 'latest',
				'region'      => $region,
				'credentials' => $credentials,
			]);

			// Add year/month to the directory if enabled.
			if ($this->params->get('append_path_user', 0))
			{
				// Always populate the list based on active user.
				$folder .= Factory::getApplication()->getIdentity()->id;
				$folder .= '/';
			}

			// Add year/month to the directory if enabled.
			if ($this->params->get('append_path', 0))
			{
				// In case of an edit, we check for the sermon_date and choose the year/month of the sermon.
				$folder .= ($ts = strtotime($this->form->getValue('sermon_date'))) ? date('Y', $ts) . '/' . date('m', $ts) : date('Y') . '/' . date('m');
				$folder .= '/';
			}

			// Add language to the directory if enabled.
			if ($this->params->get('append_path_lang', 0))
			{
				// In case of an edit, we check for the language set, otherwise we use the active language.
				$language = $this->form->getValue('language');
				$jlang    = Factory::getLanguage();
				$folder   .= ($language && ($language != '*')) ? $language : $jlang->getTag();
				$folder   .= '/';
			}

			$bucket_contents = $s3->listObjectsV2([
				'Bucket' => $bucket,
				'Prefix' => $folder,
			])['contents'];

			// Fallback to root if folder doesn't exist
			if (!$bucket_contents)
			{
				$bucket_contents = $s3->listObjects(['Bucket' => $bucket])['Contents'];
			}

			// Show last modified files first
			if ($bucket_contents)
			{
				uasort(
					$bucket_contents,
					function ($a, $b) {
						return strtotime($b['LastModified']) - strtotime($a['LastModified']);
					}
				);
			}

			$prefix = ($region === 'us-east-1') ? 's3' : 's3-' . $region;
			$domain = $prefix . '.amazonaws.com/' . $bucket;

			foreach ($bucket_contents as $file)
			{
				$option['value'] = $s3->getObjectUrl($bucket, $file['Key']);
				$option['text']  = $file['Key'];
				$options[]       = $option;
			}

			return $options;
		}
		elseif ($this->mode == 3)
		{
			$options = array();
			$url     = $this->params->get('extern_path');

			if ($xml = simplexml_load_file($url))
			{
				foreach ($xml->file as $file)
				{
					$option['value'] = $file->URL;
					$option['text']  = $file->name;
					$options[]       = $option;
				}

				return $options;
			}
		}

		return array();
	}
}
