<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\MediaField;

defined('_JEXEC') or die();

class MediawrapperField extends MediaField
{
	public $type = 'MediaWrapper';

	protected function getInput(): string
	{
		$params = ComponentHelper::getParams('com_sermonspeaker');

		$directory = match ($this->fieldname)
		{
			'picture' => $params->get('path_sermonpic'),
			'pic' => $params->get('path_speakerpic'),
			default => $params->get('path_avatar'),
		};

		$directory = trim($directory, ' /');

		if (str_starts_with($directory, 'images'))
		{
			$directory = substr($directory, 7);
		}

		$this->directory = $directory;
		$this->value     = trim($this->value, ' /');

		return parent::getInput();
	}
}
