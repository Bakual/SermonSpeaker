<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerSerie extends BaseController
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
		$app = Factory::getApplication();
		$id  = $app->input->get('id', 0, 'int');
		$app->redirect(Route::_(SermonspeakerHelperRoute::getSerieRoute($id) . '&layout=download'));
	}
}
