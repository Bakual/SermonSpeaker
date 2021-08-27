<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

JLoader::discover('SermonspeakerHelper', JPATH_SITE . '/components/com_sermonspeaker/helpers');

$list = modSermonspeakerHelper::getList($params);

if (!count($list))
{
	return;
}

$mode         = (int) $params->get('mode');
$helperMethod = $mode ? 'getSerieRoute' : 'getSpeakerRoute';

require ModuleHelper::getLayoutPath('mod_sermonspeaker', $params->get('layout', 'default'));
