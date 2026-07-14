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

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     FormField::setup()
	 * @since   7.0.4
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result === true)
		{
			$params = ComponentHelper::getParams('com_sermonspeaker');

			$directory = match ($this->fieldname)
			{
				'audiofile' => $params->get('path_audio'),
				'videofile' => $params->get('path_video'),
				'addfile' => $params->get('path_addfile'),
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
		}

		return $result;
	}
}
