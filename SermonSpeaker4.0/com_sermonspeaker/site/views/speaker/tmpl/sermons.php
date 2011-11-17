<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$player = new SermonspeakerHelperPlayer($this->items);
?>
<div class="ss-speaker-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" >
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->speaker->slug).'&layout=sermons'); ?>"><?php echo $this->speaker->name; ?></a></h2>
<?php if (in_array('speaker:hits', $this->col_speaker)): ?>
	<dl class="article-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php if (in_array('speaker:hits', $this->col_speaker)): ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS').': '.$this->speaker->hits; ?>
		</dd>
	<?php endif; ?>
	</dl>
<?php endif; ?>
<div class="category-desc">
	<div class="ss-pic">
		<?php if ($this->speaker->pic) : ?>
			<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->speaker->pic); ?>" title="<?php echo $this->speaker->name; ?>" alt="<?php echo $this->speaker->name; ?>" />
		<?php endif; ?>
	</div>
	<?php if (($this->speaker->bio && in_array('speaker:bio', $this->col_speaker)) || ($this->speaker->intro && in_array('speaker:intro', $this->col_speaker))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		if (in_array('speaker:intro', $this->col_speaker)):
			echo JHTML::_('content.prepare', $this->speaker->intro);
		endif;
		if (in_array('speaker:bio', $this->col_speaker)):
			echo JHTML::_('content.prepare', $this->speaker->bio);
		endif;
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->speaker->website && $this->speaker->website != 'http://') : ?>
		<a href="<?php echo $this->speaker->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->speaker->name); ?></a>
	<?php endif; ?>
</div>
<?php if (in_array('speaker:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-speaker-player">
		<hr class="ss-speaker-player" />
		<?php if ($player->player != 'PixelOut'): ?>
			<div id="playing">
				<img id="playing-pic" class="picture" src="" />
				<span id="playing-duration" class="duration"></span>
				<div class="text">
					<span id="playing-title" class="title"></span>
					<span id="playing-desc" class="desc"></span>
				</div>
				<span id="playing-error" class="error"></span>
			</div>
		<?php endif;
		echo $player->mspace;
		echo $player->script;
		?>
		<hr class="ss-speaker-player" />
	<?php if ($player->toggle): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('filter_field')) :?>
		<fieldset class="filters">
			<legend class="hidelabeltxt">
				<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
			</legend>
			<div class="filter-search">
				<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('JGLOBAL_FILTER_LABEL').'&nbsp;'; ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			</div>
	<?php endif;
	if ($this->params->get('show_pagination_limit')) : ?>
			<div class="display-limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&nbsp;
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
	<?php endif;
	if ($this->params->get('filter_field')) : ?>
		</fieldset>
	<?php endif; ?>
	<?php if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
<!-- Begin Data - Sermons -->
		<table class="category">
		<!-- Tabellenkopf mit Sortierlinks erstellen -->
			<thead><tr>
				<?php if (in_array('speaker:num', $this->columns)) : ?>
					<th class="num">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<th class="ss-title">
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder); ?>
				</th>
				<?php if (in_array('speaker:scripture', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'sermon_scripture', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:date', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:length', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:series', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:addfile', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:hits', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
			</tr></thead>
	<!-- Begin Data -->
			<tbody>
				<?php foreach($this->items as $i => $item) : ?>
					<tr id="sermon<?php echo $i; ?>" class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
						<?php if (in_array('speaker:num', $this->columns)) : ?>
							<td class="num">
								<?php echo $item->sermon_number; ?>
							</td>
						<?php endif; ?>
						<td class="ss-title">
							<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
						</td>
						<?php if (in_array('speaker:scripture', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
							</td>
						<?php endif;
						if (in_array('speaker:date', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo JHTML::date($item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?>
							</td>
						<?php endif;
						if (in_array('speaker:length', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
							</td>
						<?php endif;
						if (in_array('speaker:series', $this->columns)) : ?>
							<td class="ss-col">
								<?php if ($item->series_state): ?>
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
										<?php echo $item->series_title; ?>
									</a>
								<?php else:
									echo $item->series_title;
								endif; ?>
							</td>
						<?php endif;
						if (in_array('speaker:addfile', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
							</td>
						<?php endif;
						if (in_array('speaker:hits', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo $item->hits; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif;
	if ($this->params->get('show_pagination') && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
</div>