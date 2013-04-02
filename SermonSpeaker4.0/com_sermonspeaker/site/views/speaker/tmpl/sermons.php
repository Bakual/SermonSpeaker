<?php
defined('_JEXEC') or die;
JHtml::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder	= $this->state_sermons->get('list.ordering');
$listDirn	= $this->state_sermons->get('list.direction');
$player = SermonspeakerHelperSermonspeaker::getPlayer($this->sermons);
$version	= new JVersion;
$j30		= ($version->isCompatible(3.0)) ? '30' : '';
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug).'&layout=sermons'); ?>"><?php echo $this->item->name; ?></a></h2>
<?php if ($canEdit || ($canEditOwn && ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?>
		</li>
	</ul>
<?php endif;
if ($this->params->get('show_category_title', 0) || in_array('speaker:hits', $this->columns)): ?>
	<dl class="article-info speaker-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php if ($this->params->get('show_category_title', 0)): ?>
		<dd class="category-name">
			<?php echo JText::_('JCATEGORY').': '.$this->category->title; ?>
		</dd>
	<?php endif;
	if (in_array('speaker:hits', $this->columns)): ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS').': '.$this->item->hits; ?>
		</dd>
	<?php endif; ?>
	</dl>
<?php endif; ?>
<div class="category-desc">
	<div class="ss-pic">
		<?php if ($this->item->pic) : ?>
			<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->pic); ?>" title="<?php echo $this->item->name; ?>" alt="<?php echo $this->item->name; ?>" />
		<?php endif; ?>
	</div>
	<?php if (($this->item->bio && in_array('speaker:bio', $this->columns)) || ($this->item->intro && in_array('speaker:intro', $this->columns))) : ?>
		<h3><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_BIO'); ?></h3>
		<?php 
		if (in_array('speaker:intro', $this->columns)):
			echo JHtml::_('content.prepare', $this->item->intro);
		endif;
		if (in_array('speaker:bio', $this->columns)):
			echo JHtml::_('content.prepare', $this->item->bio);
		endif;
	endif; ?>
	<div class="clear-left"></div>
	<?php if ($this->item->website && $this->item->website != 'http://') : ?>
		<a class="badge badge-info" href="<?php echo $this->item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>"><?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $this->item->name); ?></a>
	<?php endif; ?>
</div>
<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) : ?>
	<div class="ss-speaker-player">
		<hr class="ss-speaker-player" />
		<?php if ($player->player != 'PixelOut'): ?>
			<div id="playing">
				<img id="playing-pic" class="picture" src="" alt="" />
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
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
	<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
		echo $this->loadTemplate('filters'.$j30);
	endif;
	if (!count($this->sermons)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
<!-- Begin Data - Sermons -->
		<table class="category table table-striped table-hover table-condensed">
		<!-- Tabellenkopf mit Sortierlinks erstellen -->
			<thead><tr>
				<?php if (in_array('speaker:num', $this->col_sermon)) : ?>
					<th class="num">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<th class="ss-title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder); ?>
				</th>
				<?php if (in_array('speaker:scripture', $this->col_sermon)) : ?>
					<th class="ss-col ss-scripture">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'book', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:date', $this->col_sermon)) : ?>
					<th class="ss-col ss-date">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:length', $this->col_sermon)) : ?>
					<th class="ss-col ss-length">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:series', $this->col_sermon)) : ?>
					<th class="ss-col ss-series">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:addfile', $this->col_sermon)) : ?>
					<th class="ss-col ss-addfile">
						<?php echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:hits', $this->col_sermon)) : ?>
					<th class="ss-col ss-hits">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('speaker:download', $this->col_sermon)) : 
					$prio	= $this->params->get('fileprio'); ?>
					<th class="ss-col ss-dl"></th>
				<?php endif; ?>
			</tr></thead>
	<!-- Begin Data -->
			<tbody>
				<?php foreach($this->sermons as $i => $item) : ?>
					<tr id="sermon<?php echo $i; ?>" class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
						<?php if (in_array('speaker:num', $this->col_sermon)) : ?>
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
						<?php if (in_array('speaker:scripture', $this->col_sermon)) : ?>
							<td class="ss-col ss-scripture">
								<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '<br />');
								echo JHtml::_('content.prepare', $scriptures); ?>
							</td>
						<?php endif;
						if (in_array('speaker:date', $this->col_sermon)) : ?>
							<td class="ss-col ss-date">
								<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
									echo JHtml::date($item->sermon_date, JText::_($this->params->get('date_format')), true);
								endif; ?>
							</td>
						<?php endif;
						if (in_array('speaker:length', $this->col_sermon)) : ?>
							<td class="ss-col ss-length">
								<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
							</td>
						<?php endif;
						if (in_array('speaker:series', $this->col_sermon)) : ?>
							<td class="ss-col ss-series">
								<?php if ($item->series_state): ?>
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
										<?php echo $item->series_title; ?>
									</a>
								<?php else:
									echo $item->series_title;
								endif; ?>
							</td>
						<?php endif;
						if (in_array('speaker:addfile', $this->col_sermon)) : ?>
							<td class="ss-col ss-addfile">
								<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
							</td>
						<?php endif;
						if (in_array('speaker:hits', $this->col_sermon)) : ?>
							<td class="ss-col ss-hits">
								<?php echo $item->hits; ?>
							</td>
						<?php endif;
						if (in_array('speaker:download', $this->col_sermon)) : 
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
	if ($this->params->get('show_pagination') && ($this->pag_sermons->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pag_sermons->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pag_sermons->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
</div>