<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Autotweet
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * SermonSpeaker integration plugin for SocialBacklinks from JoomUnited
 *
 * @since 1.0.0
 */
class plgSocialbacklinksSermonspeaker extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Register the plugin to SocialBacklinks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function onSBPluginRegister()
	{
		if (!JComponentHelper::isEnabled('com_socialbacklinks'))
		{
			return;
		}

		JLoader::register('PlgSBSermonspeakerAdapter', JPATH_ROOT . '/plugins/socialbacklinks/sermonspeaker/adapter.php');
		SBPlugin::register(new PlgSBSermonspeakerAdapter($this));
	}
}
