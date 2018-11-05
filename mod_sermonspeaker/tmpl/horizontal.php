<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var int                       $itemid
 * @var stdClass                  $module
 */

$i     = 0;
$count = count($list);
?>
<div class="sermonspeaker<?php echo $moduleclass_sfx; ?>">
	<div class="row-fluid">
		<?php foreach ($list as $row) : ?>
			<?php $i++; ?>
			<div class="sermonspeaker_entry<?php echo $i; ?> text-center span<?php echo (int) 12 / $count; ?>">
				<?php if ($row->pic) : ?>
					<a href="<?php echo JRoute::_($baseURL . $row->slug . '&Itemid=' . $itemid); ?>">
						<img src="<?php echo $row->pic; ?>" class="img-polaroid">
					</a>
				<?php endif; ?>
				<h3>
					<a href="<?php echo JRoute::_($baseURL . $row->slug . '&Itemid=' . $itemid); ?>">
						<?php echo $row->title; ?>
					</a>
				</h3>
			</div>
		<?php endforeach; ?>
	</div>
</div>