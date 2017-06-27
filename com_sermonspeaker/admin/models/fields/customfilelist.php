<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('filelist');

/**
 * Creates the filelist dropdown for sermon file select
 *
 * @since ?
 */
class JFormFieldCustomFileList extends JFormFieldFileList
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
	 * Filetype: audio or video
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	private $filetypes;

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
		$filename = JFile::stripExt(basename($this->value));

		if ($filename != ApplicationHelper::stringURLSafe($filename))
		{
			$html .= '<div class="alert alert-warning">'
				. '<button type="button" class="close" data-dismiss="alert">&times;</button>'
				. '<span class="icon-notification"></span> '
				. JText::_('COM_SERMONSPEAKER_FILENAME_NOT_IDEAL')
				. '</div>';
		}

		$html .= '<div class="input-group">
					<span class="input-group-btn"> 
						<button class="btn btn-secondary" 
							type="button" onclick="toggleElement(\'' . $this->fieldname . '\', 0);">
							<span id="' . $this->fieldname . '_text_icon" class="icon-radio-checked"></span>
						</button>
					</span>
					<input name="' . $this->name . '" id="' . $this->id . '_text" class="form-control ' . $this->class . '" value="'
						. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" type="text">';

		// Add Lookup button if not addfile field
		if ($this->file != 'addfile')
		{
			$html .= '<span class="input-group-btn">
						<button class="btn btn-secondary hasTooltip" 
							type="button" onclick="lookup(document.getElementById(\'' . $this->id . '_text\'))" 
							title="' . JText::_('COM_SERMONSPEAKER_LOOKUP') . '">
							<span class="icon-wand"></span>
						 </button>
					</span>';
		}

		// Add Google Picker if enabled and not audio field
		if ($this->params->get('googlepicker') && $this->file != 'audio')
		{
			$html .= '<span class="input-group-btn">
						<button class="btn btn-secondary hasTooltip"
							type="button" onclick="create' . ucfirst($this->file) . 'Picker();" 
							title="' . JText::_('COM_SERMONSPEAKER_GOOGLEPICKER_TIP') . '">
							<img src="' . JUri::root() . 'media/com_sermonspeaker/icons/16/drive.png">
						</button>
					</span>';
		}

		$html .= '</div>
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-secondary"
							type="button" onclick="toggleElement(\'' . $this->fieldname . '\', 1);"> 
							<span id="' . $this->fieldname . '_icon" class="icon-radio-unchecked"></span>
						</button>
					</span>';


		$html .= parent::getInput();

		if (!$this->mode && $this->file != 'addfile')
		{
			$html .= '<span class="input-group-btn">
						<button class="btn btn-secondary hasTooltip" 
							type="button" onclick="lookup(document.getElementById(\'' . $this->id . '\'))"
							title="' . JText::_('COM_SERMONSPEAKER_LOOKUP') . '">
							<span class="icon-wand"></span>
						</button>
					</span>';
		}

		$html .= '</div>';

		$html .= $this->getUploader();

		return $html;
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
			// Fallback to 'path' for B/C with versions < 5.0.3
			$dir = trim($this->params->get('path_' . $this->file, $this->params->get('path', 'images')), '/');

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
				$jlang    = JFactory::getLanguage();
				$append   = ($language && ($language != '*')) ? '/' . $language : '/' . $jlang->getTag();

				// Check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT . '/' . $dir . $append) ? $dir . $append : $dir;
			}

			$this->directory = $dir;

			// Set file filter from params
			$this->filetypes = $this->params->get($this->file . '_filetypes');

			if ($this->filetypes)
			{
				$this->filetypes = array_map('trim', explode(',', $this->filetypes));
				$filter          = '\.' . implode('$|\.', $this->filetypes) . '$';
				$this->filter    = $filter;
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
				/** @noinspection PhpUndefinedFieldInspection */
				foreach ($xml->video as $video)
				{
					/** @noinspection PhpUndefinedFieldInspection */
					$option['value'] = $video->url;
					/** @noinspection PhpUndefinedFieldInspection */
					$option['text'] = $video->title;
					$options[]      = $option;
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
			$bucket       = $this->params->get('s3_bucket');

			// Instantiate the class
			$s3 = (new \Aws\Sdk)->createMultiRegionS3([
				'version'     => '2006-03-01',
				'credentials' => [
					'key'    => $awsAccessKey,
					'secret' => $awsSecretKey,
				],
			]);

			$folder = '';

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
				$jlang    = JFactory::getLanguage();
				$folder .= ($language && ($language != '*')) ? $language : $jlang->getTag();
				$folder .= '/';
			}

			$bucket_contents = $s3->listObjects(['Bucket' => $bucket, 'Delimiter' => $folder])['Contents'];

			// Fallback to root if folder doesn't exist
			if (!$bucket_contents)
			{
				$bucket_contents = $s3->listObjects(['Bucket' => $bucket])['Contents'];
			}

			// Show last modified files first
			uasort(
				$bucket_contents,
				function ($a, $b)
				{
					return $b['LastModified']->date - $a['LastModified']->date;
				}
			);

			// TODO: Need to take care of VirtualHosts, see https://github.com/aws/aws-sdk-php/issues/347
			if ($this->params->get('s3_custom_bucket'))
			{
				$domain = $bucket;
			}
			else
			{
				$region = $s3->getBucketLocation(['Bucket' => $bucket])['LocationConstraint'];
				$prefix = ($region == 'US') ? 's3' : 's3-' . $region;
			}

			foreach ($bucket_contents as $file)
			{
				// Don't show the "folder"
				if (substr($file['Key'], -1) == '/')
				{
					continue;
				}

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
				/** @noinspection PhpUndefinedFieldInspection */
				foreach ($xml->file as $file)
				{
					/** @noinspection PhpUndefinedFieldInspection */
					$option['value'] = $file->URL;
					/** @noinspection PhpUndefinedFieldInspection */
					$option['text'] = $file->name;
					$options[]      = $option;
				}

				return $options;
			}
		}

		return array();
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
		JHtml::_('jquery.framework');
		JHtml::_('script', 'com_sermonspeaker/plupload/plupload.full.min.js', array('relative' => true));

		// Load localisation
		$tag  = str_replace('-', '_', JFactory::getLanguage()->getTag());
		$path = '/media/com_sermonspeaker/js/plupload/i18n/';
		$file = $tag . '.js';

		if (file_exists(JPATH_SITE . '/' . $path . $file))
		{
			JHtml::_('script', 'com_sermonspeaker/plupload/i18n/' . $file, array('relative' => true));
		}
		else
		{
			$tag_array = explode('_', $tag);
			$file      = $tag_array[0] . '.js';

			if (file_exists(JPATH_SITE . '/' . $path . $file))
			{
				JHtml::_('script', 'com_sermonspeaker/plupload/i18n/' . $file, array('relative' => true));
			}
		}

		$uploadURL = JUri::base() . 'index.php?option=com_sermonspeaker&task=file.upload&'
			. JSession::getFormToken() . '=1&format=json';

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
							{title : "' . JText::_($text, 'true') . '", extensions : "' . $types . '"},
						]
					},';
		}

		$plupload_script .= '
				});

				uploader_' . $this->fieldname . '.init();
				var closeButton = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>";

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
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-error");
							jQuery("#" + file.id + "_progress").replaceWith(" &raquo; ' . JText::_('ERROR') . ': " + data.error + closeButton);
						}
					}
				});

				uploader_' . $this->fieldname . '.bind("Error", function(up, err) {
					document.getElementById("filelist_' . $this->fieldname . '").innerHTML += "<div class=\"alert alert-error\">Error #"
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
		JFactory::getDocument()->addScriptDeclaration($plupload_script);

		$html = '<div id="plupload_' . $this->fieldname . '" class="uploader">
					<div id="filelist_' . $this->fieldname . '" class="filelist"></div>
					<button type="button" id="browse_' . $this->fieldname . '" class="btn btn-secondary">'
			. JText::_('COM_SERMONSPEAKER_UPLOAD')
			. '</button>
				</div>';

		return $html;
	}
}
