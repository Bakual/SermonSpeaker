<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Speakerlist Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldS3list extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'S3list';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		//include the S3 class   
		require_once('../../s3/S3.php');
		//AWS access info   
		$awsAccessKey 	= $params->get('s3_access_key');
		$awsSecretKey 	= $params->get('s3_secret_key');
		$bucket			= $params->get('path');
		//instantiate the class
		$s3 = new S3(awsAccessKey, awsSecretKey);

		$bucket_contents = $s3->getBucket($bucket);
		foreach ($bucket_contents as $file){
			$fname = $file['name'];
			$furl = 'http://'.$bucket'.s3.amazonaws.com/'.$fname;
			$options[]['value'] = $furl;
			$options[]['text'] = $fname;
		}   

		$options = $db->loadObjectList();

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
