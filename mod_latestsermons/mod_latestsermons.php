<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Helper\ModuleHelper;

/** @var \Joomla\Registry\Registry $params */

require_once __DIR__ . '/helper.php';

JLoader::discover('SermonspeakerHelper', JPATH_SITE . '/components/com_sermonspeaker/helpers');

$list = ModLatestsermonsHelper::getList($params);

if (!count($list))
{
	return;
}

$itemid          = (int) $params->get('ls_mo_menuitem');

require ModuleHelper::getLayoutPath('mod_latestsermons', $params->get('layout', 'default'));
