<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Controller\ToolsController;

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
	 * @throws \Exception
	 * @since ?
	 */
	public function createAutomatic()
	{
		$admin_controller = new ToolsController;
		$admin_controller->createAutomatic();
		$this->setRedirect(Uri::root());
	}
}
