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
JFormHelper::loadFieldClass('list');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Speakerlist Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldPlayer extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Player';

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

		$files	= JFolder::files(JPATH_SITE.'/components/com_sermonspeaker/helpers/player', '^[^_]*\.php$', false, true);
		foreach ($files as $file)
		{
			require_once($file);
			$value		= JFile::stripExt(JFile::getName($file));
			$classname	= 'SermonspeakerHelperPlayer'.ucfirst($value);
			$class		= new $classname();
			$text		= $class->getName();
			$options[$value]	= $text;
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

//		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_SERMONSPEAKER_SELECT_SPEAKER')));

		return $options;
	}
}
