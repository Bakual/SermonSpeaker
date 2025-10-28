<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class ScriptController extends BaseController
{
	/**
	 * Creates sermons automatically
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	public function createAutomatic()
	{
		$this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models');
		require_once JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/controllers/tools.php';
		$admin_controller = new SermonspeakerControllerTools;
		$admin_controller->createAutomatic();
		$this->setRedirect(Uri::root());
	}
}
