<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once __DIR__ . '/helper.php';

$cacheparams = new stdClass;
$cacheparams->cachemode = 'safeuri';
$cacheparams->class = 'modRelatedSermonsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = array('id' => 'int', 'Itemid' => 'int');

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!count($list))
{
	return;
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$showDate		 = $params->get('showDate', 0);

require JModuleHelper::getLayoutPath('mod_related_sermons', $params->get('layout', 'default'));
