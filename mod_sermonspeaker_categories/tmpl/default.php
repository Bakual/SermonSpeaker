<?php
/**
 * @package     Joomla.Site
 * @subpackage  MOD_SERMONSPEAKER_categories
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

if (!$list)
{
	return;
}

?>
<ul class="mod-sermonspeakercategories categories-module mod-list">
<?php require ModuleHelper::getLayoutPath('mod_sermonspeaker_categories', $params->get('layout', 'default') . '_items'); ?>
</ul>
