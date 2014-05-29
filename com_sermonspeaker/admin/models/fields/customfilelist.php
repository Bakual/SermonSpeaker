<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

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
	 * Method to get the field input markup for the custom filelist.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   5.1.2
	 */
	protected function getInput()
	{
		$this->params	= JComponentHelper::getParams('com_sermonspeaker');

		// Get and sanitize file parameter
		$this->file		= (string) $this->element['file'];
		$this->file		= (in_array($this->file, array('audio', 'video', 'addfile'))) ? $this->file : 'audio';

		// Mode: 0 = Default, 1 = Vimeo, 2 = Amazon S3, 3 = Extern
		$this->mode = $this->params->get('path_mode_' . $this->file, 0);

		$html	= '';

		// Check Filename for possible problems
		$filename	= JFile::stripExt(basename($this->value));

		if ($filename != JApplicationHelper::stringURLSafe($filename) && $filename != str_replace(' ', '_', JFile::makeSafe($filename)))
		{
			$html .= '<div class="alert alert-warning">'
						. '<button type="button" class="close" data-dismiss="alert">&times;</button>'
						. '<span class="icon-notification"></span> '
						. JText::_('COM_SERMONSPEAKER_FILENAME_NOT_IDEAL')
					. '</div>';
		}

		$html	.= '<div class="input-prepend input-append">'
					. '<div id="' . $this->fieldname . '_text_icon" class="btn add-on icon-checkmark" onclick="toggleElement(\'' . $this->fieldname . '\', 0);"> </div>'
					. '<input name="' . $this->name . '" id="' . $this->id . '_text" class="' . $this->class . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" type="text">';

		// Add Lookup button if not addfile field
		if ($this->file != 'addfile')
		{
			$html	.= '<div class="btn add-on hasTooltip icon-wand" onclick="lookup(document.getElementById(\'' . $this->id . '_text\'))" title="'
						. JText::_('COM_SERMONSPEAKER_LOOKUP') . '"> </div>';
		}

		// Add Google Picker if enabled and not audio field
		if ($this->params->get('googlepicker') && $this->file != 'audio')
		{
			$html	.= '<div class="btn add-on hasTooltip" onclick="create' . ucfirst($this->file) . 'Picker();" title="' . JText::_('COM_SERMONSPEAKER_GOOGLEPICKER_TIP') . '">'
							. '<img src="' . JURI::root() . 'media/com_sermonspeaker/icons/16/drive.png">'
						. '</div>';
		}

		$html	.= '</div>'
				. '<br />'
				. '<div class="input-prepend input-append">'
					. '<div id="' . $this->fieldname . '_icon" class="btn add-on icon-cancel" onclick="toggleElement(\'' . $this->fieldname . '\', 1);"> </div>';

		$html	.= parent::getInput();

		if (!$this->mode && $this->file != 'addfile')
		{
			$html	.= '<div class="btn add-on hasTooltip icon-wand" onclick="lookup(document.getElementById(\'' . $this->id . '\'))" title="' . JText::_('COM_SERMONSPEAKER_LOOKUP') . '"> </div>';
		}

		$html	.= '</div>';

		return $html;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Get and sanitize file parameter
		$this->file		= (string) $this->element['file'];
		$this->file		= (in_array($this->file, array('audio', 'video', 'addfile'))) ? $this->file : 'audio';
		 // Mode: 0 = Default, 1 = Vimeo, 2 = Amazon S3, 3 = Extern
		$this->mode = $this->params->get('path_mode_'.$this->file, 0);

		if (!$this->mode)
		{
			// Fallback to 'path' for B/C with versions < 5.0.3
			$dir	= trim($this->params->get('path_'.$this->file, $this->params->get('path', 'images')), '/');

			// Add year/month to the directory if enabled.
			if ($this->params->get('append_path', 0))
			{
				// In case of an edit, we check for the sermon_date and choose the year/month of the sermon.
				$append = ($ts = strtotime($this->form->getValue('sermon_date'))) ? '/'.date('Y', $ts).'/'.date('m', $ts) : '/'.date('Y').'/'.date('m');
				// check if directory exists, fallback to base directory if not.
				$dir = is_dir(JPATH_ROOT.'/'.$dir.$append) ? $dir.$append : $dir;
			}
			// Add language to the directory if enabled.
			if ($this->params->get('append_path_lang', 0))
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
			$this->filetypes	= $this->params->get($this->file.'_filetypes');
			if ($this->filetypes)
			{
				$this->filetypes	= array_map('trim', explode(',', $this->filetypes));
				$filter		= '\.'.implode('$|\.', $this->filetypes).'$';
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
		elseif ($this->mode == 1)
		{
			$options = array();
			$url = 'http://vimeo.com/api/v2/'.$this->params->get('vimeo_id').'/videos.xml';
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
		elseif ($this->mode == 2)
		{
			// Initialize variables.
			$options = array();

			//include the S3 class   
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/s3/S3.php';
			//AWS access info   
			$awsAccessKey 	= $this->params->get('s3_access_key');
			$awsSecretKey 	= $this->params->get('s3_secret_key');
			$bucket			= $this->params->get('s3_bucket');
			//instantiate the class
			$s3		= new S3($awsAccessKey, $awsSecretKey);
			$region	= $s3->getBucketLocation($bucket);
			$prefix	= ($region == 'US') ? 's3' : 's3-'.$region;

			$bucket_contents = $s3->getBucket($bucket);
			foreach ($bucket_contents as $this->file)
			{
				$fname = $this->file['name'];
				$furl = 'http://'.$prefix.'.amazonaws.com/'.$bucket.'/'.$fname;
				$option['value'] = $furl;
				$option['text'] = $fname;
				$options[] = $option;
			}   

			return $options;
		}
		elseif ($this->mode == 3)
		{
			$options = array();
			$url = $this->params->get('extern_path');
			if ($xml = simplexml_load_file($url))
			{
				foreach ($xml->file as $this->file)
				{
					$option['value'] = $this->file->URL;
					$option['text'] = $this->file->name;
					$options[] = $option;
				}   
				return $options;
			}
		}
	}
}
