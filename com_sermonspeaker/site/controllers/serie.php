<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerSerie extends JControllerLegacy
{
	/**
	 * Redirecting to new AJAX based download function for backward compatibility
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	public function download()
	{
		$app = JFactory::getApplication();
		$id  = $app->input->get('id', 0, 'int');
		$app->redirect(JRoute::_(SermonspeakerHelperRoute::getSerieRoute($id) . '&layout=download'));
	}
}
