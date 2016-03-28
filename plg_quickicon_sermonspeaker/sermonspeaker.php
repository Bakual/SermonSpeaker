<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Quickicon
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

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
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_sermonspeaker'))
		{
			return;
		}

		$buttons = array();

		if ($this->params->get('sermons', 1))
		{
			$buttons[] = array(
				'link' => 'index.php?option=com_sermonspeaker&task=sermon.add',
				'image' => 'pencil-2',
				'text' => JText::_('PLG_QUICKICON_SERMONSPEAKER_NEW_SERMON'),
				'id' => 'plg_quickicon_sermonspeaker_new_sermon',
				'access' => array('core.create', 'com_sermonspeaker'),
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
			$buttons[] = array(
				'link' => 'index.php?option=com_sermonspeaker&view=sermons',
				'image' => 'quote-3',
				'text' => JText::_('PLG_QUICKICON_SERMONSPEAKER_SERMONS_MANAGER'),
				'id' => 'plg_quickicon_sermonspeaker_sermons_manager',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}

		if ($this->params->get('speakers', 1))
		{
			$buttons[] = array(
				'link'  => 'index.php?option=com_sermonspeaker&task=speaker.add',
				'image' => 'pencil-2',
				'text'  => JText::_('PLG_QUICKICON_SERMONSPEAKER_NEW_SPEAKER'),
				'access' => array('core.create', 'com_sermonspeaker'),
				'id'    => 'plg_quickicon_sermonspeaker_new_speaker',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
			$buttons[] = array(
				'link'  => 'index.php?option=com_sermonspeaker&view=speakers',
				'image' => 'users',
				'text'  => JText::_('PLG_QUICKICON_SERMONSPEAKER_SPEAKERS_MANAGER'),
				'id'    => 'plg_quickicon_sermonspeaker_speakers_manager',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}
		if ($this->params->get('series', 1))
		{
			$buttons[] = array(
				'link'  => 'index.php?option=com_sermonspeaker&task=serie.add',
				'image' => 'pencil-2',
				'text'  => JText::_('PLG_QUICKICON_SERMONSPEAKER_NEW_SERIE'),
				'access' => array('core.create', 'com_sermonspeaker'),
				'id'    => 'plg_quickicon_sermonspeaker_new_serie',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
			$buttons[] = array(
				'link'  => 'index.php?option=com_sermonspeaker&view=series',
				'image' => 'drawer-2',
				'text'  => JText::_('PLG_QUICKICON_SERMONSPEAKER_SERIES_MANAGER'),
				'id'    => 'plg_quickicon_sermonspeaker_series_manager',
				'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
			);
		}

		return $buttons;
	}
}
