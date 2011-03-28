<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div id="ss-archive-container">
<h1 class="componentheading"><?php echo $this->title; ?></h1>
<?php if (in_array('archive:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-archive-player">
		<hr class="ss-archive-player" />
	<?php
	echo $this->player['mspace'];
	echo $this->player['script'];
	?>
		<hr class="ss-archive-player" />
	<?php if ($this->params->get('fileswitch')): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onClick="Video()" Alt="Video" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onClick="Audio()" Alt="Audio" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<?php if (empty($this->items)) :?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif; ?>
	<table class="category">
	<!-- Create the headers with sorting links -->
		<thead><tr>
			<?php if (in_array('archive:num', $this->columns)) : ?>
				<th class="num">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
				</th>
			<?php endif; ?>
			<th class="ss-title">
				<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder); ?>
			</th>
			<?php if (in_array('archive:scripture', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('archive:speaker', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('archive:date', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('archive:length', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONLENGTH', 'sermon_time', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('archive:series', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('archive:addfile', $this->columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
				</th>
			<?php endif; ?>
		</tr></thead>
	<!-- Begin Data -->
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<tr id="sermon<?php echo $i; ?>" class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
					<?php if (in_array('archive:num', $this->columns)) : ?>
						<td class="num">
							<?php echo $item->sermon_number; ?>
						</td>
					<?php endif; ?>
					<td class="ss-title">
						<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item); ?>
					</td>
					<?php if (in_array('archive:scripture', $this->columns)) : ?>
						<td class="ss-col">
							<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
						</td>
					<?php endif;
					if (in_array('archive:speaker', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name); ?>
						</td>
					<?php endif;
					if (in_array('archive:date', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?>
						</td>
					<?php endif;
					if (in_array('archive:length', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
						</td>
					<?php endif;
					if (in_array('archive:series', $this->columns)) : ?>
						<td class="ss_col">
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
								<?php echo $item->series_title; ?>
							</a>
						</td>
					<?php endif;
					if (in_array('archive:addfile', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
						</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</div>
</form>
</div>
<?php endif; ?>