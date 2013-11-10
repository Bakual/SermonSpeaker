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
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// get and sanitize file parameter
		$file	= (string) $this->element['file'];
		$file	= (in_array($file, array('audio', 'video', 'addfile'))) ? $file : 'audio';

		$params	= JComponentHelper::getParams('com_sermonspeaker');

		$mode = $params->get('path_mode_'.$file, 0); // 0 = Default, 1 = Vimeo, 2 = Amazon S3, 3 = Extern
		if (!$mode)
		{
			// Fallback to 'path' for B/C with versions < 5.0.3
			$dir	= trim($params->get('path_'.$file, $params->get('path', 'images')), '/');

			// Add year/month to the directory if enabled.
			if ($params->get('append_path', 0))
			{
				// In case of an edit, we check for the sermon_date and choose the year/month of the sermon.
				$append = ($ts = strtotime($this->form->getValue('sermon_date'))) ? '/'.date('Y', $ts).'/'.date('m', $ts) : '/'.date('Y').'/'.date('m');
				// check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT.'/'.$dir.$append) ? $dir.$append : $dir;
			}
			// Add language to the directory if enabled.
			if ($params->get('append_path_lang', 0))
			{
				// In case of an edit, we check for the language set, otherwise we use the active language.
				$language = $this->form->getValue('language');
				$jlang = JFactory::getLanguage();
				$append = ($language && ($language != '*')) ? '/'.$language : '/'.$jlang->getTag();
				// check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT.'/'.$dir.$append) ? $dir.$append : $dir;
			}
			$this->directory = $dir;

			// Set file filter from params
			$filetypes	= $params->get($file.'_filetypes');
			if ($filetypes)
			{
				$filetypes	= array_map('trim', explode(',', $filetypes));
				$filter		= '\.'.implode('$|\.', $filetypes).'$';
				$this->filter = $filter;
			}

			// Get the field options.
			$options = parent::getOptions();

			// Add directory to the value.
			foreach ($options as $option)
			{
				$option->value = '/'.$dir.'/'.$option->value;
			}
			return $options;
		}
		elseif ($mode == 1)
		{
			$options = array();
			$url = 'http://vimeo.com/api/v2/'.$params->get('vimeo_id').'/videos.xml';
			if ($xml = simplexml_load_file($url))
			{
				foreach ($xml->video as $video)
				{
					$option['value'] = $video->url;
					$option['text'] = $video->title;
					$options[] = $option;
				}   

				return $options;
			}
		}
		elseif ($mode == 2)
		{
			// Initialize variables.
			$options = array();

			//include the S3 class   
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/s3/S3.php';
			//AWS access info   
			$awsAccessKey 	= $params->get('s3_access_key');
			$awsSecretKey 	= $params->get('s3_secret_key');
			$bucket			= $params->get('s3_bucket');
			//instantiate the class
			$s3		= new S3($awsAccessKey, $awsSecretKey);
			$region	= $s3->getBucketLocation($bucket);
			$prefix	= ($region == 'US') ? 's3' : 's3-'.$region;

			$bucket_contents = $s3->getBucket($bucket);
			foreach ($bucket_contents as $file)
			{
				$fname = $file['name'];
				$furl = 'http://'.$prefix.'.amazonaws.com/'.$bucket.'/'.$fname;
				$option['value'] = $furl;
				$option['text'] = $fname;
				$options[] = $option;
			}   

			return $options;
		}
		elseif ($mode == 3)
		{
			$options = array();
			$url = $params->get('extern_path');
			if ($xml = simplexml_load_file($url))
			{
				foreach ($xml->file as $file)
				{
					$option['value'] = $file->URL;
					$option['text'] = $file->name;
					$options[] = $option;
				}   
				return $options;
			}
		}
	}
}
