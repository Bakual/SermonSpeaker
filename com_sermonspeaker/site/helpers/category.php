<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * SermonSpeaker Component Category Tree
 *
 * @since  5
 */
class SermonspeakerCategories extends JCategories
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  Obtions
	 */
	public function __construct($options = array())
	{
		if (!isset($options['table']))
		{
			$options['table'] = '#__sermon_sermons';
		}

		$options['extension'] = 'com_sermonspeaker';

		parent::__construct($options);
	}
}
