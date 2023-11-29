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
use Joomla\CMS\Uri\Uri;

$jinput = Factory::getApplication()->input;

// Providing backward compatibilty to older SermonSpeaker versions
if ($jinput->get('task') == 'podcast')
{
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . Uri::root() . 'index.php?option=com_sermonspeaker&view=feed&format=raw');

	return;
}

// Register Helperclasses for autoloading
JLoader::discover('SermonspeakerHelper', JPATH_BASE . '/components/com_sermonspeaker/helpers');

// Load Composer Autoloader
require_once(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/vendor/autoload.php');

// Load languages and merge with fallbacks
$jlang = Factory::getApplication()->getLanguage();
$jlang->load('com_sermonspeaker', JPATH_BASE . '/components/com_sermonspeaker', 'en-GB', true);
$jlang->load('com_sermonspeaker', JPATH_BASE . '/components/com_sermonspeaker', null, true);

$controller = BaseController::getInstance('Sermonspeaker');
$controller->execute($jinput->get('task'));
$controller->redirect();
