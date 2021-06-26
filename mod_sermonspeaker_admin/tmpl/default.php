<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermonspeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$header = count($list) - 1;
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
?>
<?php if ($list) : ?>
	<?php foreach ($list as $type => $items) : ?>
		<?php if ($header) : ?>
			<div class="nav-header"><?php echo Text::_('MOD_SERMONSPEAKER_' . $type); ?></div>
		<?php endif; ?>
		<div class="row-striped">
			<?php foreach ($items as $i => $item) : ?>
				<div class="row-fluid">
					<div class="span9">
						<?php if ($params->get('show_state', 1)) : ?>
							<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, '', false); ?>
						<?php endif; ?>
						<?php if ($params->get('show_hits')) : ?>
							<?php $hits = (int) $item->hits; ?>
							<?php $hits_class = ($hits >= 10000 ? 'important' : ($hits >= 1000 ? 'warning' : ($hits >= 100 ? 'info' : ''))); ?>
							<span class="badge badge-<?php echo $hits_class; ?> hasTooltip" title="<?php echo Text::_('JGLOBAL_HITS'); ?>">
								<?php echo $item->hits; ?>
							</span>
						<?php endif; ?>
						<?php if ($item->checked_out) : ?>
								<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
						<?php endif; ?>

						<strong class="row-title break-word">
							<?php if ($item->link) : ?>
								<a href="<?php echo $item->link; ?>">
									<?php echo htmlspecialchars($item->title, ENT_QUOTES); ?></a>
							<?php else : ?>
								<?php echo htmlspecialchars($item->title, ENT_QUOTES); ?>
							<?php endif; ?>
						</strong>

						<?php if ($params->get('show_author', 1)) : ?>
							<small class="hasTooltip" title="<?php echo Text::_('JGLOBAL_FIELD_CREATED_BY_LABEL'); ?>">
								<?php echo $item->author_name; ?>
							</small>
						<?php endif; ?>
						<?php if ($params->get('show_counts')) : ?>
							<?php if (isset($item->sermons)) : ?>
								<a href="index.php?option=com_sermonspeaker&view=sermons&filter[<?php echo rtrim($type, 's'); ?>]=<?php echo $item->id; ?>">
									<span class="badge badge-info">
										<?php echo Text::_('MOD_SERMONSPEAKER_SERMONS'); ?>: <?php echo $item->sermons; ?>
									</span>
								</a>
							<?php endif; ?>
							<?php if (isset($item->series)) : ?>
								<a href="index.php?option=com_sermonspeaker&view=series">
									<span class="badge badge-info">
										<?php echo Text::_('MOD_SERMONSPEAKER_SERIES'); ?>: <?php echo $item->series; ?>
									</span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="span3">
						<div class="small pull-right hasTooltip" title="<?php echo HTMLHelper::_('tooltipText', 'JGLOBAL_FIELD_CREATED_LABEL'); ?>">
							<span class="icon-calendar" aria-hidden="true"></span>
							<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC5')); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="alert"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
		</div>
	</div>
<?php endif; ?>
