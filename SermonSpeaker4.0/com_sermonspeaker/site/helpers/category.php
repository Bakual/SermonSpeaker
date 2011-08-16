<?php
/**
 * @package		com_sermonspeaker
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * SermonSpeaker Component Category Tree
 *
 * @static
 * @package		com_sermonspeaker
 */
class SermonspeakerCategories extends JCategories
{
	public function __construct($options = array())
	{
		if (!isset($options['table'])){
			$options['table'] = '#__sermon_sermons';
		}
		$options['extension'] = 'com_sermonspeaker';
		parent::__construct($options);
	}
}
