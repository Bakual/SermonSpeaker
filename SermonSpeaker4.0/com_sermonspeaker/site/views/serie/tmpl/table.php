<?php
defined('_JEXEC') or die('Restricted access');
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$player = new SermonspeakerHelperPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-serie-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->slug)); ?>"><?php echo $this->item->series_title; ?></a></h2>
<?php if ($canEdit || ($canEditOwn && ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?>
		</li>
	</ul>
<?php endif;
if ($this->params->get('show_category_title', 0) || in_array('serie:hits', $this->col_serie) || in_array('serie:speaker', $this->col_serie)): ?>
	<dl class="article-info serie-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php if ($this->params->get('show_category_title', 0)): ?>
		<dd class="category-name">
			<?php echo JText::_('JCATEGORY').': '.$this->category->title; ?>
		</dd>
	<?php endif;
	if (in_array('serie:speaker', $this->col_serie) && $this->item->speakers) : ?>
		<dd class="createdby">
			<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS').': '.$this->item->speakers; ?>
		</dd>
	<?php endif;
	if (in_array('serie:hits', $this->col_serie)): ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS').': '.$this->item->hits; ?>
		</dd>
	<?php endif;
	if (in_array('serie:download', $this->col_serie)) : ?>
		<dd class="hits">
			<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL').': '; ?>
			<a href="<?php echo JRoute::_('index.php?task=serie.download&id='.$this->item->slug); ?>" target="_new" title="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
			<img src="media/com_sermonspeaker/images/download.png" alt="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>" />
		</a></dd>
	<?php endif; ?>
	</dl>
<?php endif;
if (in_array('serie:description', $this->col_serie)): ?>
	<div class="category-desc">
		<div class="ss-avatar">
			<?php if ($this->item->avatar) : ?>
				<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->avatar); ?>">
			<?php endif; ?>
		</div>
		<?php echo JHTML::_('content.prepare', $this->item->series_description); ?>
		<div class="clear-left"></div>
	</div>
<?php endif;
if (in_array('serie:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-serie-player">
		<hr class="ss-serie-player" />
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
		<hr class="ss-serie-player" />
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
			<div class="filter-select">
				<label class="filter-select-lbl" for="filter-select"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL').'&nbsp;'; ?></label>
				<select name="book" id="filter_books" class="inputbox" onchange="this.form.submit()">
					<?php echo JHtml::_('select.options', $this->books, 'value', 'text', $this->state->get('scripture.book'), true);?>
				</select>
				<select name="month" id="filter_months" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
					
					<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state->get('date.month'), true);?>
				</select>
				<select name="year" id="filter_years" class="inputbox" onchange="this.form.submit()">
					<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR'); ?></option>
					<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state->get('date.year'), true);?>
				</select>
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
					<th class="ss-col ss-scripture">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:speaker', $this->columns)) : ?>
					<th class="ss-col ss-speaker">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:date', $this->columns)) : ?>
					<th class="ss-col ss-date">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:length', $this->columns)) : ?>
					<th class="ss-col ss-length">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:addfile', $this->columns)) : ?>
					<th class="ss-col ss-addfile">
						<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:hits', $this->columns)) : ?>
					<th class="ss-col ss-hits">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:download', $this->columns)) : 
					$prio	= $this->params->get('fileprio'); ?>
					<th class="ss-col ss-dl"></th>
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
							<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player);
							if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) : ?>
								<ul class="actions">
									<li class="edit-icon">
										<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
									</li>
								</ul>
							<?php endif; ?>
						</td>
						<?php if (in_array('serie:scripture', $this->columns)) : ?>
							<td class="ss-col ss-scripture">
								<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '<br />');
								echo JHTML::_('content.prepare', $scriptures); ?>
							</td>
						<?php endif;
						if (in_array('serie:speaker', $this->columns)) : ?>
							<td class="ss-col ss-speaker">
								<?php if ($item->speaker_state):
									echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
								else:
									echo $item->name;
								endif; ?>
							</td>
						<?php endif;
						if (in_array('serie:date', $this->columns)) : ?>
							<td class="ss-col ss-date">
								<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
									echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), true);
								endif; ?>
							</td>
						<?php endif;
						if (in_array('serie:length', $this->columns)) : ?>
							<td class="ss-col ss-length">
								<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
							</td>
						<?php endif;
						if (in_array('serie:addfile', $this->columns)) : ?>
							<td class="ss-col ss-addfile">
								<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
							</td>
						<?php endif;
						if (in_array('serie:hits', $this->columns)) : ?>
							<td class="ss-col ss-hits">
								<?php echo $item->hits; ?>
							</td>
						<?php endif;
						if (in_array('serie:download', $this->columns)) : 
							$file = ($item->videofile && ($prio || !$item->audiofile)) ? 'video' : 'audio'; ?>
							<td class="ss-col ss-dl">
								<?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, $file, 1); ?>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
</div>