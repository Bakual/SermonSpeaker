<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
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
$id    = 'sermonspeakerCarousel' . $module->id;

JHtml::_('bootstrap.carousel');
?>
<div id="<?php echo $id; ?>" class="sermonspeaker<?php echo $moduleclass_sfx; ?> carousel slide">
	<div id="sermonspeakerCarousel<?php echo $module->id; ?>" class="sermonspeaker_list">
		<ol class="carousel-indicators">
			<?php for ($j = 0; $j < $count; $j++): ?>
				<li data-target="#<?php echo $id; ?>"
					data-slide-to="<?php echo $j; ?>"<?php echo ($j) ? '' : ' class="active"'; ?>></li>
			<?php endfor; ?>
		</ol>
		<div class="carousel-inner">
			<?php foreach ($list as $i => $row) : ?>
				<?php $link = JRoute::_($baseURL . $row->slug . '&Itemid=' . $itemid); ?>
				<div class="sermonspeaker_entry<?php echo $i; ?> item <?php echo ($i) ? '' : 'active'; ?>">
					<h4><a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
						</a></h4>
					<div style="clear:left;"></div>
					<?php if (strlen($row->tooltip) > 0) : ?>
						<div>
							<?php echo JHtml::_('content.prepare', $row->tooltip); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<a class="carousel-control left" href="#<?php echo $id; ?>" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#<?php echo $id; ?>" data-slide="next">&rsaquo;</a>
	</div>
</div>
