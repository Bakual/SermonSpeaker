<?php
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$player = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
$version	= new JVersion;
$j30		= ($version->isCompatible(3.0)) ? '30' : '';
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
			<a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$this->item->slug); ?>" class="modal hasTip" rel="{handler:'iframe',size:{x:400,y:200}}" title="::<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
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
		<?php echo JHtml::_('content.prepare', $this->item->series_description); ?>
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
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
	<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
		echo $this->loadTemplate('filters'.$j30);
	endif;
	if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<table class="category table table-striped table-hover table-condensed">
		<!-- Create the headers with sorting links -->
			<thead><tr>
				<?php if (in_array('serie:num', $this->columns)) : ?>
					<th class="num">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<th class="ss-title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder); ?>
				</th>
				<?php if (in_array('serie:scripture', $this->columns)) : ?>
					<th class="ss-col ss-scripture">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:speaker', $this->columns)) : ?>
					<th class="ss-col ss-speaker">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:date', $this->columns)) : ?>
					<th class="ss-col ss-date">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:length', $this->columns)) : ?>
					<th class="ss-col ss-length">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:addfile', $this->columns)) : ?>
					<th class="ss-col ss-addfile">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('serie:hits', $this->columns)) : ?>
					<th class="ss-col ss-hits">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
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
								echo JHtml::_('content.prepare', $scriptures); ?>
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
									echo JHtml::Date($item->sermon_date, JText::_($this->params->get('date_format')), true);
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
							$type = ($item->videofile && ($prio || !$item->audiofile)) ? 'video' : 'audio';
							$filesize = $type.'filesize'; ?>
							<td class="ss-col ss-dl">
								<?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, $type, 1, $item->$filesize); ?>
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