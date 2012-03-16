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
$limit 		= (int)$this->params->get('limit', '');
$player = new SermonspeakerHelperPlayer($this->items);
?>
<div class="ss-sermons-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->cat): ?>
	<h2><span class="subheading-category"><?php echo $this->cat; ?></span></h2>
<?php endif;
if (in_array('sermons:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-sermons-player">
		<hr class="ss-sermons-player" />
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
		<hr class="ss-sermons-player" />
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
	<?php endif;
	if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<?php foreach($this->items as $i => $item) : ?>
			<div id="sermon<?php echo $i; ?>" class="ss-entry" onclick="ss_play('<?php echo $i; ?>')">
				<?php if ($item->picture): ?>
					<div class="ss-picture"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->picture); ?>"></div>
				<?php elseif ($item->pic): ?>
					<div class="ss-picture"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->pic); ?>"></div>
				<?php endif; ?>
				<h3><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?>"><?php echo $item->sermon_title; ?></a></h3>
				<?php if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) : ?>
					<ul class="actions">
						<li class="edit-icon">
							<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
						</li>
					</ul>
				<?php endif; ?>
				<dl class="article-info sermon-info">
				<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
				<?php if (in_array('sermons:series', $this->columns) && $item->series_title) : ?>
					<dd class="category-name">
						<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
						<?php if ($item->series_state) : ?>
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
						<?php echo $this->escape($item->series_title); ?></a>
						<?php else :
							echo $this->escape($item->series_title);
						endif; ?>
					</dd>
				<?php endif;
				if (in_array('sermons:date', $this->columns)) : ?>
					<dd class="create">
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
						<?php echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), true); ?>
					</dd>
				<?php endif;
				if (in_array('sermons:speaker', $this->columns) && $item->name) : ?>
					<dd class="createdby">
						<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
						<?php if ($item->speaker_state):
							echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
						else: 
							echo $item->name;
						endif; ?>
					</dd>
				<?php endif;
				if (in_array('sermons:hits', $this->columns)) : ?>
					<dd class="hits">
						<?php echo JText::_('JGLOBAL_HITS'); ?>: 
						<?php echo $item->hits; ?>
					</dd>
				<?php endif;if (in_array('sermons:scripture', $this->columns) && $item->scripture) : ?>
					<dd class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
						<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
						echo JHTML::_('content.prepare', $scriptures); ?>
					</dd>
				<?php endif;
				if ($this->params->get('custom1') && $item->custom1) : ?>
					<dd class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
						<?php echo $item->custom1; ?>
					</dd>
				<?php endif;
				if ($this->params->get('custom2') && $item->custom2) : ?>
					<dd class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
						<?php echo $item->custom2; ?>
					</dd>
				<?php endif;
				if (in_array('sermons:length', $this->columns)) : ?>
					<dd class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
						<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
					</dd>
				<?php endif;
				if (in_array('sermons:addfile', $this->columns) && $item->addfile) : ?>
					<dd class="ss-sermondetail-info">
						<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
						<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
					</dd>
				<?php endif; ?>
				</dl>
				<div style="clear:left"></div>
				<?php if (strlen($item->notes) > 0) : ?>
					<div>
						<?php echo JHTML::_('content.prepare', $item->notes); ?>
					</div>
				<?php endif;
				if (in_array('sermons:download', $this->columns)) : ?>
					<div class="ss-dl">
						<?php if ($item->audiofile):
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'audio', 0);
						endif;
						if ($item->videofile):
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'video', 0);
						endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<hr class="ss-sermons-player" style="clear:both" />
		<?php endforeach;
	endif;
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