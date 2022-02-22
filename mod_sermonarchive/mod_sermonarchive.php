<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonArchive
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

JLoader::register('SermonspeakerHelperRoute', JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php');

$list = modSermonarchiveHelper::getList($params);

if (!count($list))
{
	return;
}

require ModuleHelper::getLayoutPath('mod_sermonarchive', $params->get('layout', 'default'));
