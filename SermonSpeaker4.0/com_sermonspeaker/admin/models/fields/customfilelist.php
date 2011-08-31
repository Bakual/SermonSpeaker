<?php
/**
 * @version		$Id: imagelist.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('filelist');

/**
 * Supports an HTML select list of image
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldCustomFileList extends JFormFieldFileList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'CustomFileList';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$this->params = JComponentHelper::getParams('com_sermonspeaker');
		switch ($this->fieldname){
			case 'audiofile':
				$this->mode = $this->params->get('path_mode_audio', 0);
				break;
			case 'videofile':
				$this->mode = $this->params->get('path_mode_video', 0);
				break;
			default:
				$this->mode = 0;
				break;
		}
		if (!$this->mode){
			// Strip the path from the value so a matching filename gets selected.
			$this->value = substr(strrchr($this->value, '/'), 1);
		}
		return parent::getInput();
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		if (!$this->mode){
			// Define the image file type filter.
			$path	= (string) $this->element['path'];
			$dir	= $this->params->get($path);

			// Set the form field element attribute for file type filter.

			// Add year/month to the directory if enabled.
			if ($this->params->get('append_path', 0)){
				// In case of an edit, we check for the sermon_date and choose the year/month of the sermon.
				$append = ($ts = strtotime($this->form->getValue('sermon_date'))) ? $append	= DS.date('Y', $ts).DS.date('m', $ts) : $append = DS.date('Y').DS.date('m');
				// check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT.'/'.$dir.$append) ? $dir.$append : $dir;
			}
			$this->element->addAttribute('directory', $dir);

			// Get the field options.

			return parent::getOptions();
		} elseif ($this->mode == 1){
			$options = array();
			$url = 'http://vimeo.com/api/v2/'.$this->params->get('vimeo_id').'/videos.xml';
			if ($xml = simplexml_load_file($url)){
				foreach ($xml->video as $video) {
					$option['value'] = $video->url;
					$option['text'] = $video->title;
					$options[] = $option;
				}   

				return $options;
			}
		} elseif ($this->mode == 2){
			// Initialize variables.
			$options = array();

			//include the S3 class   
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/s3/S3.php';
			//AWS access info   
			$awsAccessKey 	= $this->params->get('s3_access_key');
			$awsSecretKey 	= $this->params->get('s3_secret_key');
			$bucket			= $this->params->get('s3_bucket');
			//instantiate the class
			$s3 = new S3($awsAccessKey, $awsSecretKey);

			$bucket_contents = $s3->getBucket($bucket);
			foreach ($bucket_contents as $file){
				$fname = $file['name'];
				$furl = 'http://'.$bucket.'.s3.amazonaws.com/'.$fname;
				$option['value'] = $furl;
				$option['text'] = $fname;
				$options[] = $option;
			}   

			return $options;
		} elseif ($this->mode == 3){
			$options = array();
			$url = $this->params->get('extern_path');
			if ($xml = simplexml_load_file($url)){
				foreach ($xml->file as $file) {
					$option['value'] = $file->URL;
					$option['text'] = $file->name;
					$options[] = $option;
				}   
				return $options;
			}
		}
	}
}
