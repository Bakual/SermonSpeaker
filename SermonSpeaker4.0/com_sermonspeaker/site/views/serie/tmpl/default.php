<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div class="ss-serie-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->serie->slug)); ?>"><?php echo $this->serie->series_title; ?></a></h2>
<?php if ($this->cat || in_array('serie:hits', $this->col_serie)): ?>
	<dl class="article-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php if ($this->cat): ?>
		<dd class="category-name">
			<?php echo JText::_('JCATEGORY').': '.$this->cat; ?>
		</dd>
	<?php endif;
	if (in_array('serie:hits', $this->col_serie)): ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS').': '.$this->serie->hits; ?>
		</dd>
	<?php endif;
	if (in_array('serie:download', $this->col_serie)) : ?>
		<dd class="hits">
			<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL').': '; ?>
			<a href="<?php echo JRoute::_('index.php?task=serie.download&id='.$this->serie->id); ?>" title="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
			<img src="media/com_sermonspeaker/images/download.png" alt="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>" />
		</a></dd>
	<?php endif; ?>
	</dl>
<?php endif;
if (in_array('serie:description', $this->col_serie)): ?>
	<div class="category-desc">
		<div class="ss-avatar">
			<?php if ($this->serie->avatar) : ?>
				<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->serie->avatar); ?>">
			<?php endif; ?>
		</div>
		<?php echo JHTML::_('content.prepare', $this->serie->series_description); ?>
		<div class="clear-left"></div>
	</div>
<?php endif;
if (in_array('serie:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-serie-player">
		<hr class="ss-serie-player" />
	<?php
	echo $this->player['mspace'];
	echo $this->player['script'];
	?>
		<hr class="ss-serie-player" />
	<?php if ($this->params->get('fileswitch')): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" />
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
		<table class="category">
		<!-- Create the headers with sorting links -->
			<thead><tr>
				<?php if (in_array('serie:num', $this->columns)) : ?>
					<th class="num">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<th class="ss-title">
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder); ?>
				</th>
				<?php if (in_array('serie:scripture', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'sermon_scripture', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:speaker', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:date', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:length', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:addfile', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:hits', $this->columns)) : ?>
					<th class="ss-col">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
			</tr></thead>
		<!-- Begin Data -->
			<tbody>
				<?php foreach($this->items as $i => $item) : ?>
					<tr id="sermon<?php echo $i; ?>" class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
						<?php if (in_array('serie:num', $this->columns)) : ?>
							<td class="num">
								<?php echo $item->sermon_number; ?>
							</td>
						<?php endif; ?>
						<td class="ss-title">
							<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item); ?>
						</td>
						<?php if (in_array('serie:scripture', $this->columns)) : ?>
							<td class="ss-col">
								<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
							</td>
						<?php endif;
						if (in_array('serie:speaker', $this->columns)) : ?>
							<td class="ss_col">
								<?php if ($item->speaker_state):
									echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
								else:
									echo $item->name;
								endif; ?>
							</td>
						<?php endif;
						if (in_array('serie:date', $this->columns)) : ?>
							<td class="ss_col">
								<?php echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?>
							</td>
						<?php endif;
						if (in_array('serie:length', $this->columns)) : ?>
							<td class="ss_col">
								<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
							</td>
						<?php endif;
						if (in_array('serie:addfile', $this->columns)) : ?>
							<td class="ss_col">
								<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
							</td>
						<?php endif;
						if (in_array('serie:hits', $this->columns)) : ?>
							<td class="ss_col">
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