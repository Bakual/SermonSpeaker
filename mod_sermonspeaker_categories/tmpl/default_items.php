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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$type          = $params->get('parent_type', 'sermons');
$routeFunction = 'get' . $type . 'Route';
$input         = $app->input;
$option        = $input->getCmd('option');
$view          = $input->getCmd('view');
$catid         = $input->getInt('catid');

foreach ($list as $item) : ?>
	<li<?php if ($catid == $item->id && $view == $type && $option == 'com_sermonspeaker') echo ' class="active"'; ?>> <?php $levelup = $item->level - $startLevel - 1; ?>
		<a href="<?php echo Route::_(SermonspeakerHelperRoute::$routeFunction($item->id, $item->language)); ?>">
		<?php echo $item->title; ?>
			<?php if ($params->get('numitems')) : ?>
				(<?php echo $item->numitems; ?>)
			<?php endif; ?>
		</a>

		<?php if ($params->get('show_description', 0)) : ?>
			<?php echo HTMLHelper::_('content.prepare', $item->description, $item->getParams(), 'mod_sermonspeaker_categories.content'); ?>
		<?php endif; ?>
		<?php if ($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0)
			|| ($params->get('maxlevel') >= ($item->level - $startLevel)))
			&& count($item->getChildren())) : ?>
			<?php echo '<ul>'; ?>
			<?php $temp = $list; ?>
			<?php $list = $item->getChildren(); ?>
			<?php require ModuleHelper::getLayoutPath('mod_sermonspeaker_categories', $params->get('layout', 'default') . '_items'); ?>
			<?php $list = $temp; ?>
			<?php echo '</ul>'; ?>
		<?php endif; ?>
	</li>
<?php endforeach; ?>
