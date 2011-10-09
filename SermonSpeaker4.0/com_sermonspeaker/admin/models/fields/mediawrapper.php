<?php
/**
 * @package		SermonSpeaker
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * Wrapper for standard Media Formfield, just passes a directory set in the SermonSpeaker options
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('media');

class JFormFieldMediaWrapper extends JFormFieldMedia
{
	public $type = 'MediaWrapper';

	protected function getInput()
	{
		$this->params = JComponentHelper::getParams('com_sermonspeaker');
		switch ($this->fieldname){
			case 'picture':
				$directory = $this->params->get('path_sermonpic');
				break;
			case 'pic':
				$directory = $this->params->get('path_speakerpic');
				break;
			case 'avatar':
			default:
				$directory = $this->params->get('path_avatar');
				break;
		}
		$directory = trim($directory, ' /');
		if (strpos ($directory, 'images') === 0){
			$directory = substr($directory, 7);
		}
		$this->element['directory'] = $directory;

		$this->value = trim($this->value, ' /');

		return parent::getInput();
	}
}
