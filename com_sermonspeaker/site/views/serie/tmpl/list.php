<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
$prio       = $this->params->get('fileprio');
?>
<div class="com-sermonspeaker-serie<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-serie-list">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'serie')); ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
			  id="adminForm" class="com-sermonspeaker-serie__sermons">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
				</div>
			<?php else : ?>
				<ul class="list-group list-group-flush">
					<?php foreach ($this->items as $i => $item) : ?>
						<?php $sep = 0; ?>
						<li id="sermon<?php echo $i; ?>"
							class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?> sermon-item list-group-item">
							<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit">
									<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
							</strong>
							<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
							<?php if (in_array('serie:download', $this->columns)) : ?>
								<?php $type = ($item->videofile and ($prio || !$item->audiofile)) ? 'video' : 'audio'; ?>
								<?php $filesize = $type . 'filesize'; ?>
								<span class="ss-dl ms-1 float-end">
									<?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, $type, 3, $item->$filesize); ?>
								</span>
							<?php endif; ?>
							<?php if (in_array('serie:hits', $this->columns)) : ?>
								<span class="ss-hits badge bg-info float-end">
									<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif; ?>
							<br/>
							<?php if (in_array('serie:speaker', $this->columns) and $item->speaker_title) : ?>
								<?php $sep = 1; ?>
								<small class="ss-speaker">
									<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
									<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
								</small>
							<?php endif; ?>

							<?php if (in_array('serie:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
								<?php if ($sep) : ?>
									|
								<?php endif; ?>
								<?php $sep = 1; ?>
								<small class="ss-length">
									<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
									<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
								</small>
							<?php endif; ?>

							<?php if (in_array('serie:scripture', $this->columns) and $item->scripture) : ?>
								<?php if ($sep) : ?>
									|
								<?php endif; ?>
								<?php $sep = 1; ?>
								<small class="ss-scripture">
									<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
									<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; '); ?>
									<?php echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
								</small>
							<?php endif; ?>

							<?php if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
								<span class="ss-date float-end">
									<small class="text-muted">
										<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
									</small>
								</span>
							<?php endif; ?>

							<?php if (in_array('serie:notes', $this->columns) and $item->notes) : ?>
								<div class="ss-notes">
									<?php echo HTMLHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
								</div>
							<?php endif; ?>

							<?php if (in_array('serie:maintext', $this->columns) and $item->maintext) : ?>
								<div class="ss-maintext">
									<?php echo HTMLHelper::_('content.prepare', $item->maintext, '', 'com_sermonspeaker.maintext'); ?>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HTMLHelper::_('icon.create', $this->category, $this->params); ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'serie', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<input type="hidden" name="limitstart" value=""/>
		</form>
	</div>
</div>
