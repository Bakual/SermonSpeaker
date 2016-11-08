<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerScript extends JControllerLegacy
{
	/**
	 * Creates sermons automatically
	 *
	 * @return  void
	 */
	public function createAutomatic()
	{
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . '/models');
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/controllers/tools.php';
		$admin_controller = new SermonspeakerControllerTools;
		$admin_controller->createAutomatic();
		$this->setRedirect(JUri::root());

		return;
	}
}
