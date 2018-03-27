<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermonspeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

$header = count($list) - 1;
JHtml::_('bootstrap.tooltip');
?>
<?php if ($list) : ?>
	<?php foreach ($list as $type => $items) : ?>
		<?php if ($header) : ?>
			<div class="nav-header"><?php echo JTExt::_('MOD_SERMONSPEAKER_' . $type); ?></div>
		<?php endif; ?>
		<div class="row-striped">
			<?php foreach ($items as $i => $item) : ?>
				<div class="row-fluid">
					<div class="span9">
						<?php if ($params->get('show_state', 1)) : ?>
							<?php echo JHtml::_('jgrid.published', $item->state, $i, '', false); ?>
						<?php endif; ?>
						<?php if ($params->get('show_hits')) : ?>
							<?php $hits = (int) $item->hits; ?>
							<?php $hits_class = ($hits >= 10000 ? 'important' : ($hits >= 1000 ? 'warning' : ($hits >= 100 ? 'info' : ''))); ?>
							<span class="badge badge-<?php echo $hits_class; ?> hasTooltip" title="<?php echo JText::_('JGLOBAL_HITS'); ?>">
								<?php echo $item->hits; ?>
							</span>
						<?php endif; ?>
						<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
						<?php endif; ?>

						<strong class="row-title break-word">
							<?php if ($item->link) : ?>
								<a href="<?php echo $item->link; ?>">
									<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></a>
							<?php else : ?>
								<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
							<?php endif; ?>
						</strong>

						<?php if ($params->get('show_author', 1)) : ?>
							<small class="hasTooltip" title="<?php echo JText::_('JGLOBAL_FIELD_CREATED_BY_LABEL'); ?>">
								<?php echo $item->author_name; ?>
							</small>
						<?php endif; ?>
					</div>
					<div class="span3">
						<span class="small">
							<span class="icon-calendar"></span>
							<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="alert"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
		</div>
	</div>
<?php endif; ?>
