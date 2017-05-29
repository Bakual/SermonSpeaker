<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Helper class for SermonUpload module
 *
 * @since  1.0
 */
abstract class ModSermonuploadHelper
{
	/**
	 * Loads JavaScript for the uploader into Document Header
	 *
	 * @param   string                    $identifier  Unique identifier
	 * @param   string                    $type        Filetype (audio, video, addfile)
	 * @param   Joomla/Registry/Registry  $type        SermonSpeaker params
	 *
	 * @return  void
	 */
	public static function loadUploaderScript($identifier, $type, $params)
	{
		$identifier = $identifier . $type;
		$uploadURL = JUri::base() . 'index.php?option=com_sermonspeaker&task=file.upload&'
			. JSession::getFormToken() . '=1&format=json';

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
		$text  = strtoupper('COM_SERMONSPEAKER_FIELD_' . $identifier . '_LABEL');

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

				uploader_' . $identifier . '.init();
				var closeButton = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>";

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
						var data = JSON.parse(response.response);
						if (data.status == 1){
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-success");
							document.getElementById(file.id).innerHTML = data.error + closeButton;
						}else{
							jQuery("#" + file.id).removeClass("alert-info").addClass("alert-error");
							jQuery("#" + file.id + "_progress").replaceWith(" &raquo; ' . JText::_('ERROR') . ': " + data.error + closeButton);
						}
					}
				});

				uploader_' . $identifier . '.bind("Error", function(up, err) {
					document.getElementById("filelist_' . $identifier . '").innerHTML += "<div class=\"alert alert-error\">Error #"
						+ err.code + ": " + err.message + closeButton + "</div>";
				});

				uploader_' . $identifier . '.bind("PostInit", function(up) {
					jQuery("#upload-noflash").remove();
					if(up.features.dragdrop){
						jQuery("#' . $identifier . '_drop").addClass("drop-area");
					}
				});
			});
		';
		JFactory::getDocument()->addScriptDeclaration($plupload_script);
	}

	/**
	 * Function to determine max upload value
	 *
	 * @return  string  Lower PHP Setting Value
	 */
	static public function getMaxUploadValue()
	{
		// Check some PHP settings for upload limit so I can show it as an info
		$post_max_size       = ini_get('post_max_size');
		$upload_max_filesize = ini_get('upload_max_filesize');

		return (self::return_bytes($post_max_size) < self::return_bytes($upload_max_filesize)) ? $post_max_size : $upload_max_filesize;
	}


	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual
	 *
	 * @param   string  $val  Value from the PHP setting
	 *
	 * @return  int  $val  Value in bytes
	 */
	static private function return_bytes($val)
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
