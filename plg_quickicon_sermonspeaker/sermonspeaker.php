<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Quickicon
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * SermonSpeaker Quickicons plugin
 *
 * @since  1.0
 */
class PlgQuickiconSermonspeaker extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param   string  $context  The calling context
	 *
	 * @return  array  A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since   1.0
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !Factory::getUser()->authorise('core.manage', 'com_sermonspeaker'))
		{
			return array();
		}

		$buttons = array();

		if ($this->params->get('sermons', 1))
		{
			$buttons[] = array(
				'image' => 'fas fa-list-alt',
				'link' => 'index.php?option=com_sermonspeaker&view=sermons',
				'linkadd' => 'index.php?option=com_sermonspeaker&task=sermon.add',
				'name' => 'PLG_QUICKICON_SERMONSPEAKER_SERMONS_MANAGER',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}

		if ($this->params->get('speakers', 1))
		{
			$buttons[] = array(
				'image' => 'fas fa-comment',
				'link'  => 'index.php?option=com_sermonspeaker&view=speakers',
				'linkadd'  => 'index.php?option=com_sermonspeaker&task=speaker.add',
				'name'  => 'PLG_QUICKICON_SERMONSPEAKER_SPEAKERS_MANAGER',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}

		if ($this->params->get('series', 1))
		{
			$buttons[] = array(
				'image' => 'fas fa-object-group',
				'link'  => 'index.php?option=com_sermonspeaker&view=series',
				'linkadd'  => 'index.php?option=com_sermonspeaker&task=serie.add',
				'name'  => 'PLG_QUICKICON_SERMONSPEAKER_SERIES_MANAGER',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}

		return $buttons;
	}
}
