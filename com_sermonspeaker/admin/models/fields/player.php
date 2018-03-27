<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

/**
 * Playerlist field class for the SermonSpeaker.
 *
 * @package  SermonSpeaker
 * @since    4.0
 */
class JFormFieldPlayer extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string $type Name of the field
	 *
	 * @since ?
	 */
	protected $type = 'Player';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since ?
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = parent::getOptions();

		$files = JFolder::files(JPATH_SITE . '/components/com_sermonspeaker/helpers/player', '^[^_]*\.php$', false, true);

		foreach ($files as $file)
		{
			require_once $file;
			$value     = basename($file, '.php');
			$classname = 'SermonspeakerHelperPlayer' . ucfirst($value);
			/** @var SermonspeakerHelperPlayer $class */
			$class           = new $classname;
			$text            = $class->getName();
			$options[$value] = $text;
		}

		if (is_numeric($this->value))
		{
			switch ($this->value)
			{
				case 1:
					$this->value = 'pixelout';
					break;
				case 2:
					$this->value = 'flowplayer3';
					break;
				case 0:
				default:
					$this->value = 'jwplayer5';
					break;
			}
		}

		return $options;
	}
}
