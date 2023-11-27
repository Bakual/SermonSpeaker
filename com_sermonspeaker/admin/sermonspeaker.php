<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check.
if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_sermonspeaker'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Register Helperclass for autoloading
JLoader::register('SermonspeakerHelper', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

// Load Composer Autoloader
require_once(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/vendor/autoload.php');

HTMLHelper::_('stylesheet', 'com_sermonspeaker/sermonspeaker-admin.css', array('relative' => true));

$controller = BaseController::getInstance('Sermonspeaker');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
