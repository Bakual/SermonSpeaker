<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\MediaField;

class JFormFieldMediaWrapper extends MediaField
{
	public $type = 'MediaWrapper';

	protected function getInput()
	{
		$params = ComponentHelper::getParams('com_sermonspeaker');
		switch ($this->fieldname)
		{
			case 'picture':
				$directory = $params->get('path_sermonpic');
				break;
			case 'pic':
				$directory = $params->get('path_speakerpic');
				break;
			case 'avatar':
			default:
				$directory = $params->get('path_avatar');
				break;
		}
		$directory = trim($directory, ' /');
		if (strpos($directory, 'images') === 0)
		{
			$directory = substr($directory, 7);
		}
		$this->directory = $directory;

		$this->value = trim($this->value, ' /');

		return parent::getInput();
	}
}
